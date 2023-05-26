<?php

namespace FelixNagel\T3extblog\Controller;

/**
 * This file is part of the "t3extblog" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use TYPO3\CMS\Core\Messaging\AbstractMessage;
use TYPO3\CMS\Extbase\Http\ForwardResponse;
use FelixNagel\T3extblog\Domain\Repository\AbstractSubscriberRepository;
use FelixNagel\T3extblog\Service\AuthenticationServiceInterface;
use FelixNagel\T3extblog\Domain\Model\AbstractSubscriber;
use FelixNagel\T3extblog\Domain\Model\BlogSubscriber;
use FelixNagel\T3extblog\Domain\Model\PostSubscriber;
use TYPO3\CMS\Extbase\Persistence\QueryResultInterface;

/**
 * AbstractSubscriberController.
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

    public function listAction()
    {
        $this->checkAuth();

        $this->redirect('list', 'Subscriber');
    }

    public function confirmAction()
    {
        $this->checkAuth(true);

        if ($this->subscriber === null) {
            throw new \InvalidArgumentException('No authenticated subscriber given.');
        }

        $this->signalSlotDispatcher->dispatch(
            self::class,
            'subscriberConfirmAction',
            [&$this->subscriber, $this]
        );

        if ($this->subscriber->_getProperty('hidden') === true) {
            $this->subscriber->_setProperty('hidden', false);
            $this->addFlashMessageByKey('confirmed');

            $this->subscriberRepository->update($this->subscriber);
            $this->persistAllEntities();
        }

        $this->redirect('list', 'PostSubscriber');
    }

    public function deleteAction(AbstractSubscriber $subscriber = null)
    {
        $this->checkAuth();

        if (!$subscriber instanceof BlogSubscriber && !$subscriber instanceof PostSubscriber) {
            throw new \InvalidArgumentException('No subscriber given.');
        }

        $this->signalSlotDispatcher->dispatch(
            self::class,
            'subscriberDeleteAction',
            [&$this->subscriber, $this]
        );

        // Check if the given subscriber is owned by authenticated user
        if ($subscriber->getEmail() !== $this->authentication->getEmail()) {
            throw new \InvalidArgumentException('Invalid subscriber given.');
        }

        $this->subscriberRepository->remove($subscriber);
        $this->persistAllEntities();

        $this->addFlashMessageByKey('deleted', AbstractMessage::INFO);
        $this->redirect('list', 'Subscriber');
    }

    /**
     * Check and get authentication.
     */
    protected function checkAuth(bool $isConfirmRequest = false)
    {
        if ($this->hasCodeArgument()) {
            $this->authenticate($isConfirmRequest);
        }

        if ($this->authentication->isValid()) {
            return;
        }

        return (new ForwardResponse('processError'))->withControllerName('Subscriber');
    }

    /**
     * Get authentication.
     */
    protected function authenticate(bool $isConfirmRequest = false)
    {
        $rateLimitSettings = $this->settings['subscriptionManager']['rateLimit'];
        if ($rateLimitSettings['enable'] && !$this->initRateLimiter('subscriber-authenticate', $rateLimitSettings)
                ->isAccepted('subscriber-authenticate')
        ) {
            return (new ForwardResponse('processError'))->withControllerName('Subscriber')->withArguments(['message' => 'rateLimit']);
        }

        $code = $this->getAuthCode();

        /* @var $subscriber AbstractSubscriber */
        $subscriber = $this->subscriberRepository->findByCode($code, !$isConfirmRequest);

        if ($subscriber === null) {
            return (new ForwardResponse('processError'))->withControllerName('Subscriber')->withArguments(['message' => 'authFailed']);
        }

        $modify = '+1 hour';
        if (isset($this->subscriptionSettings['emailHashTimeout'])) {
            $modify = trim($this->subscriptionSettings['emailHashTimeout']);
        }

        if ($subscriber->isAuthCodeExpired($modify)) {
            return (new ForwardResponse('processError'))->withControllerName('Subscriber')->withArguments(['message' => 'linkOutdated']);
        }

        if ($isConfirmRequest) {
            $confirmedSubscriptions = $this->findExistingSubscriptions($subscriber);

            if (count($confirmedSubscriptions) > 0) {
                $subscriber->_setProperty('deleted', true);

                $this->subscriberRepository->update($subscriber);
                $this->persistAllEntities();

                return (new ForwardResponse('processError'))
                    ->withControllerName('Subscriber')
                    ->withArguments(['message' => 'alreadyRegistered', 'severity' => AbstractMessage::NOTICE]);
            }
        }

        $this->getRateLimiter()->reset('subscriber-authenticate');

        $this->authentication->login($subscriber->getEmail());
        $this->subscriber = $subscriber;
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
    protected function getAuthCode(): string
    {
        $code = $this->request->getArgument('code');

        if (strlen($code) !== 32 || !ctype_alnum($code)) {
            return (new ForwardResponse('processError'))
                ->withControllerName('Subscriber')
                ->withArguments(['message' => 'invalidLink']);
        }

        return $code;
    }

    /**
     * @param AbstractSubscriber $subscriber
     */
    abstract protected function findExistingSubscriptions($subscriber): QueryResultInterface;
}
