<?php

namespace FelixNagel\T3extblog\Service;

/**
 * This file is part of the "t3extblog" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use FelixNagel\T3extblog\Domain\Model\Post;
use FelixNagel\T3extblog\Domain\Model\BlogSubscriber;
use FelixNagel\T3extblog\Domain\Repository\BlogSubscriberRepository;
use FelixNagel\T3extblog\Domain\Repository\PostRepository;

/**
 * Handles all notification mails for new posts notification.
 */
class BlogNotificationService extends AbstractNotificationService
{
    /**
     * @var BlogSubscriberRepository
     */
    protected $subscriberRepository;

    public function initializeObject()
    {
        parent::initializeObject();

        $this->subscriberRepository = $this->objectManager->get(BlogSubscriberRepository::class);
        $this->subscriptionSettings = $this->settingsService->getTypoScriptByPath('subscriptionManager.blog');
    }

    /**
     * Process added subscriber.
     *
     * @param BlogSubscriber $subscriber
     */
    public function processNewEntity($subscriber)
    {
        if (!($subscriber instanceof BlogSubscriber)) {
            throw new \InvalidArgumentException('Object should be of type BlogSubscriber!');
        }

        $this->signalSlotDispatcher->dispatch(
            self::class,
            'processNewSubscriber',
            [&$subscriber, $this]
        );

        if ($subscriber->isValidForOptin()) {
            $this->sendOptInMail($subscriber);
            $this->persistToDatabase();
        }
    }

    /**
     * Process changed status of a subscriber.
     *
     * @param BlogSubscriber $subscriber
     */
    public function processChangedStatus($subscriber)
    {
        if (!($subscriber instanceof BlogSubscriber)) {
            throw new \InvalidArgumentException('Object should be of type BlogSubscriber!');
        }

        $this->signalSlotDispatcher->dispatch(
            self::class,
            'processChangedSubscriber',
            [&$subscriber, $this]
        );

        if ($subscriber->isValidForOptin()) {
            $this->sendOptInMail($subscriber);
            $this->persistToDatabase();
        }
    }

    /**
     * Send opt-in mail for subscriber.
     */
    protected function sendOptInMail(BlogSubscriber $subscriber)
    {
        $this->getLog()->dev('Send blog subscriber opt-in mail.');

        $subscriber->updateAuth();
        $this->subscriberRepository->update($subscriber);

        $this->sendSubscriberEmail(
            $subscriber,
            $this->translate('subject.subscriber.blog.new'),
            $this->subscriptionSettings['subscriber']['template']['confirm']
        );
    }

    /**
     * Send post notification mails.
     */
    public function notifySubscribers(Post $post): ?int
    {
        $settings = $this->subscriptionSettings['subscriber'];

        if (!$settings['enableNotifications']) {
            return null;
        }

        if ($post->getMailsSent()) {
            return null;
        }

        $subscribers = $this->subscriberRepository->findForNotification();
        $subject = $this->translate('subject.subscriber.blog.notify', $post->getTitle());
        $variables = ['post' => $post];

        $this->signalSlotDispatcher->dispatch(
            self::class,
            'notifySubscribers',
            [$post, &$subscribers, &$subject, &$variables, $this]
        );

        $this->getLog()->dev('Send blog subscriber notification mails to '.count($subscribers).' users.');

        /* @var $subscriber BlogSubscriber */
        foreach ($subscribers as $subscriber) {
            $subscriber->updateAuth();
            $this->subscriberRepository->update($subscriber);

            $this->sendSubscriberEmail($subscriber, $subject, $settings['template']['notification'], $variables);
        }

        $post->setMailsSent(true);
        $this->objectManager->get(PostRepository::class)->update($post);
        $this->persistToDatabase();

        return count($subscribers);
    }
}
