<?php

namespace FelixNagel\T3extblog\Controller;

/**
 * This file is part of the "t3extblog" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use Psr\Http\Message\ResponseInterface;
use TYPO3\CMS\Core\Type\ContextualFeedbackSeverity as Message;
use TYPO3\CMS\Extbase\Http\ForwardResponse;
use FelixNagel\T3extblog\Domain\Repository\AbstractSubscriberRepository;
use FelixNagel\T3extblog\Service\AuthenticationServiceInterface;
use FelixNagel\T3extblog\Domain\Model\AbstractSubscriber;
use FelixNagel\T3extblog\Domain\Model\BlogSubscriber;
use FelixNagel\T3extblog\Domain\Model\PostSubscriber;
use TYPO3\CMS\Extbase\Persistence\QueryResultInterface;

/**
 * AbstractSubscriberController.
 *
 * @SuppressWarnings("PHPMD.CyclomaticComplexity")
 * @SuppressWarnings("PHPMD.NPathComplexity")
 */
abstract class AbstractSubscriberController extends AbstractController
{
    /**
     * @var AbstractSubscriberRepository
     */
    protected $subscriberRepository;

    /**
     * @var AbstractSubscriber
     */
    protected $subscriber = null;

    /**
     * Contains the subscription settings.
     */
    protected array $subscriptionSettings = [];

    protected ?AuthenticationServiceInterface $authentication = null;

    public function injectAuthentication(AuthenticationServiceInterface $authentication)
    {
        $this->authentication = $authentication;
    }

    public function listAction(): ResponseInterface
    {
        if (($authResult = $this->checkAuth()) instanceof ResponseInterface) {
            return $authResult;
        }

        return $this->redirect('list', 'Subscriber');
    }

    public function confirmAction(): ResponseInterface
    {
        if (($authResult = $this->checkAuth(true)) instanceof ResponseInterface) {
            return $authResult;
        }

        if ($this->subscriber === null) {
            throw new \InvalidArgumentException('No authenticated subscriber given.');
        }

        $this->subscriber = $this->dispatchConfirmEvent($this->subscriber);

        if ($this->subscriber->_getProperty('hidden') === true) {
            $this->subscriber->_setProperty('hidden', false);
            $this->addFlashMessageByKey('confirmed');

            $this->subscriberRepository->update($this->subscriber);
            $this->persistAllEntities();
        }

        return $this->redirect('list', 'PostSubscriber');
    }

    abstract protected function dispatchConfirmEvent(AbstractSubscriber $subscriber): AbstractSubscriber;

    protected function delete(AbstractSubscriber $subscriber = null): ResponseInterface
    {
        if (($authResult = $this->checkAuth()) instanceof ResponseInterface) {
            return $authResult;
        }

        if (!$subscriber instanceof BlogSubscriber && !$subscriber instanceof PostSubscriber) {
            throw new \InvalidArgumentException('No subscriber given.');
        }

        $subscriber = $this->dispatchDeleteEvent($subscriber);

        // Check if the given subscriber is owned by authenticated user
        if ($subscriber->getEmail() !== $this->authentication->getEmail()) {
            throw new \InvalidArgumentException('Invalid subscriber given.');
        }

        $this->subscriberRepository->remove($subscriber);
        $this->persistAllEntities();

        $this->addFlashMessageByKey('deleted', Message::INFO);

        return $this->redirect('list', 'Subscriber');
    }

    abstract protected function dispatchDeleteEvent(AbstractSubscriber $subscriber): AbstractSubscriber;

    /**
     * Check authentication.
     */
    protected function checkAuth(bool $isConfirmRequest = false): ?ResponseInterface
    {
        if ($this->hasCodeArgument()) {
            if (($authResult = $this->authenticate($isConfirmRequest)) instanceof ResponseInterface) {
                return $authResult;
            }
        }

        if ($this->authentication->isValid()) {
            return null;
        }

        return (new ForwardResponse('processError'))->withControllerName('Subscriber');
    }

    /**
     * Set authentication.
     */
    protected function authenticate(bool $isConfirmRequest = false): ?ResponseInterface
    {
        $rateLimitSettings = $this->settings['subscriptionManager']['rateLimit'];
        if ($rateLimitSettings['enable'] && !$this->initRateLimiter('subscriber-authenticate', $rateLimitSettings)
                ->isAccepted('subscriber-authenticate')
        ) {
            return (new ForwardResponse('processError'))
                ->withControllerName('Subscriber')
                ->withArguments(['message' => 'rateLimit']);
        }

        $code = $this->getCodeArgument();
        if (strlen($code) !== 32 || !ctype_alnum($code)) {
            return (new ForwardResponse('processError'))
                ->withControllerName('Subscriber')
                ->withArguments(['message' => 'invalidLink']);
        };

        /* @var $subscriber AbstractSubscriber */
        $subscriber = $this->subscriberRepository->findByCode($code, !$isConfirmRequest);

        if ($subscriber === null) {
            return (new ForwardResponse('processError'))
                ->withControllerName('Subscriber')
                ->withArguments(['message' => 'authFailed']);
        }

        $modify = '+1 hour';
        if (isset($this->subscriptionSettings['emailHashTimeout'])) {
            $modify = trim($this->subscriptionSettings['emailHashTimeout']);
        }

        if ($subscriber->isAuthCodeExpired($modify)) {
            return (new ForwardResponse('processError'))
                ->withControllerName('Subscriber')
                ->withArguments(['message' => 'linkOutdated']);
        }

        if ($isConfirmRequest) {
            $confirmedSubscriptions = $this->findExistingSubscriptions($subscriber);

            if (count($confirmedSubscriptions) > 0) {
                $subscriber->_setProperty('deleted', true);

                $this->subscriberRepository->update($subscriber);
                $this->persistAllEntities();

                return (new ForwardResponse('processError'))
                    ->withControllerName('Subscriber')
                    ->withArguments(['message' => 'alreadyRegistered', 'severity' => Message::NOTICE]);
            }
        }

        $this->getRateLimiter()->reset('subscriber-authenticate');

        $this->authentication->login($subscriber->getEmail());
        $this->subscriber = $subscriber;

        return null;
    }

    /**
     * If the request has argument 'code'.
     */
    protected function hasCodeArgument(): string
    {
        return $this->request->hasArgument('code') && strlen($this->request->getArgument('code')) > 0;
    }

    /**
     * Checks the code.
     */
    protected function getCodeArgument(): string
    {
        return $this->request->getArgument('code');
    }

    /**
     * @param AbstractSubscriber $subscriber
     */
    abstract protected function findExistingSubscriptions($subscriber): QueryResultInterface;
}
