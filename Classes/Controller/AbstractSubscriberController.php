<?php

namespace FelixNagel\T3extblog\Controller;

/**
 * This file is part of the "t3extblog" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use FelixNagel\T3extblog\Service\AuthenticationServiceInterface;
use TYPO3\CMS\Core\Messaging\FlashMessage;
use FelixNagel\T3extblog\Domain\Model\AbstractSubscriber;
use FelixNagel\T3extblog\Domain\Model\BlogSubscriber;
use FelixNagel\T3extblog\Domain\Model\PostSubscriber;

/**
 * AbstractSubscriberController.
 */
abstract class AbstractSubscriberController extends AbstractController
{
    /**
     * subscriberRepository.
     */
    protected $subscriberRepository;

    /**
     * subscriber.
     *
     * @var AbstractSubscriber
     */
    protected $subscriber = null;

    /**
     * Contains the subscription settings.
     */
    protected array $subscriptionSettings = [];

    /**
     * feUserService.
     */
    protected ?AuthenticationServiceInterface $authentication = null;

    
    public function injectAuthentication(AuthenticationServiceInterface $authentication)
    {
        $this->authentication = $authentication;
    }

    /**
     * action confirm.
     */
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
            $this->addFlashMessageByKey('confirmed', FlashMessage::OK);

            $this->subscriberRepository->update($this->subscriber);
            $this->persistAllEntities();
        }

        $this->redirect('list', 'PostSubscriber');
    }

    /**
     * action delete.
     *
     * @param AbstractSubscriber $subscriber
     */
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

        $this->addFlashMessageByKey('deleted', FlashMessage::INFO);
        $this->redirect('list', 'Subscriber');
    }

    /**
     * Check and get authentication.
     *
     */
    protected function checkAuth(bool $isConfirmRequest = false)
    {
        if ($this->hasCodeArgument()) {
            $this->authenticate($isConfirmRequest);
        }

        if ($this->authentication->isValid()) {
            return;
        }

        $this->forward('processError', 'Subscriber');
    }

    /**
     * Get authentication.
     *
     */
    protected function authenticate(bool $isConfirmRequest = false)
    {
        $code = $this->getAuthCode();

        /* @var $subscriber AbstractSubscriber */
        $subscriber = $this->subscriberRepository->findByCode($code, !$isConfirmRequest);

        if ($subscriber === null) {
            $this->forward('processError', 'Subscriber', null, ['message' => 'authFailed']);
        }

        $modify = '+1 hour';
        if (isset($this->subscriptionSettings['emailHashTimeout'])) {
            $modify = trim($this->subscriptionSettings['emailHashTimeout']);
        }

        if ($subscriber->isAuthCodeExpired($modify)) {
            $this->forward('processError', 'Subscriber', null, ['message' => 'linkOutdated']);
        }

        if ($isConfirmRequest) {
            $confirmedSubscriptions = $this->findExistingSubscriptions($subscriber);

            if (count($confirmedSubscriptions) > 0) {
                $subscriber->_setProperty('deleted', true);

                $this->subscriberRepository->update($subscriber);
                $this->persistAllEntities();

                $this->forward(
                    'processError',
                    'Subscriber',
                    null,
                    ['message' => 'alreadyRegistered', 'severity' => FlashMessage::NOTICE]
                );
            }
        }

        $this->authentication->login($subscriber->getEmail());
        $this->subscriber = $subscriber;
    }

    /**
     * If the request has argument 'code'.
     *
     */
    protected function hasCodeArgument(): string
    {
        return $this->request->hasArgument('code') && strlen($this->request->getArgument('code')) > 0;
    }

    /**
     * Checks the code.
     *
     */
    protected function getAuthCode(): string
    {
        $code = $this->request->getArgument('code');

        if (strlen($code) !== 32 || !ctype_alnum($code)) {
            $this->forward('processError', 'Subscriber', null, ['message' => 'invalidLink']);
        }

        return $code;
    }

    /**
     * Finds existing subscriptions.
     *
     * @param $subscriber
     *
     * @return array|\TYPO3\CMS\Extbase\Persistence\QueryResultInterface
     */
    abstract protected function findExistingSubscriptions($subscriber);
}
