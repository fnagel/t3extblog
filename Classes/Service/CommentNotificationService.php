<?php

namespace FelixNagel\T3extblog\Service;

/**
 * This file is part of the "t3extblog" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use FelixNagel\T3extblog\Domain\Model\Post;
use FelixNagel\T3extblog\Domain\Model\Comment;
use FelixNagel\T3extblog\Domain\Model\PostSubscriber;
use FelixNagel\T3extblog\Domain\Repository\CommentRepository;
use FelixNagel\T3extblog\Domain\Repository\PostSubscriberRepository;

/**
 * Handles all notification mails for new or changed comments.
 */
class CommentNotificationService extends AbstractNotificationService
{
    /**
     * subscriberRepository.
     *
     * @var PostSubscriberRepository
     */
    protected $subscriberRepository;

    /**
     */
    public function initializeObject()
    {
        parent::initializeObject();

        $this->subscriberRepository = $this->objectManager->get(PostSubscriberRepository::class);
        $this->subscriptionSettings = $this->settingsService->getTypoScriptByPath('subscriptionManager.comment');
    }

    /**
     * Process added comment
     * Comment is already persisted to DB.
     *
     * @param Comment $comment Comment
     */
    public function processNewEntity($comment)
    {
        if (!($comment instanceof Comment)) {
            throw new \InvalidArgumentException('Object should be of type Comment!');
        }

        $this->signalSlotDispatcher->dispatch(
            self::class,
            'processNewComment',
            [&$comment, $this]
        );

        $subscriber = null;
        if ($this->isNewSubscriptionValid($comment)) {
            $subscriber = $this->addSubscriber($comment);
        }

        if ($comment->isValid()) {
            if ($subscriber instanceof PostSubscriber) {
                $this->sendOptInMail($subscriber, $comment);
            }

            $this->notifySubscribers($comment);

            $this->persistToDatabase();
            $this->flushFrontendCache($comment);
        }
    }

    /**
     * Process changed status of a comment
     * Comment is already persisted to DB.
     *
     * @param Comment $comment Comment
     */
    public function processChangedStatus($comment)
    {
        if (!($comment instanceof Comment)) {
            throw new \InvalidArgumentException('Object should be of type Comment!');
        }

        $this->signalSlotDispatcher->dispatch(
            self::class,
            'processChangedComment',
            [&$comment, $this]
        );

        if ($comment->isValid()) {
            if (!empty($comment->getEmail())) {
                $subscriber = $this->subscriberRepository->findForSubscriptionMail($comment);
                if ($subscriber instanceof PostSubscriber) {
                    $this->sendOptInMail($subscriber, $comment);
                }
            }

            $this->notifySubscribers($comment);

            $this->persistToDatabase();
            $this->flushFrontendCache($comment);
        }
    }

    /**
     * Checks if a new subscription should be added.
     *
     * @param Comment $comment
     *
     * @return bool
     */
    protected function isNewSubscriptionValid(Comment $comment)
    {
        if (empty($comment->getEmail())) {
            return false;
        }

        if (!$this->settings['blogsystem']['comments']['subscribeForComments'] || !$comment->getSubscribe()) {
            return false;
        }

        // check if user already registered
        $subscribers = $this->subscriberRepository->findExistingSubscriptions(
            $comment->getPostId(),
            $comment->getEmail()
        );
        if (count($subscribers) > 0) {
            $this->getLog()->notice('Subscriber ['.$comment->getEmail().'] already registered.');

            return false;
        }

        return true;
    }

    /**
     * Send opt-in mail for subscriber.
     *
     * @param PostSubscriber $subscriber
     * @param Comment        $comment    Comment
     */
    protected function sendOptInMail(PostSubscriber $subscriber, Comment $comment)
    {
        $this->getLog()->dev('Send subscriber opt-in mail.');

        $post = $subscriber->getPost();

        $subscriber->updateAuth();
        $this->subscriberRepository->update($subscriber);

        $this->sendSubscriberEmail(
            $subscriber,
            $this->translate('subject.subscriber.comment.new', $post->getTitle()),
            $this->subscriptionSettings['subscriber']['template']['confirm'],
            [
                'post' => $post,
                'comment' => $comment,
            ]
        );
    }

    /**
     * Add a subscriber.
     *
     * @param Comment $comment
     *
     * @return PostSubscriber
     */
    protected function addSubscriber(Comment $comment)
    {
        /* @var $newSubscriber PostSubscriber */
        $newSubscriber = $this->objectManager->get(PostSubscriber::class, $comment->getPostId());
        $newSubscriber->setEmail($comment->getEmail());
        $newSubscriber->setName($comment->getAuthor());
        $newSubscriber->setPrivacyPolicyAccepted($comment->hasPrivacyPolicyAccepted());

        $this->subscriberRepository->add($newSubscriber);
        $this->persistToDatabase(true);

        $this->getLog()->dev('Added comment subscriber uid='.$newSubscriber->getUid());

        return $newSubscriber;
    }

    /**
     * Send comment notification mails.
     *
     * @param Comment $comment
     */
    protected function notifySubscribers(Comment $comment)
    {
        $settings = $this->subscriptionSettings['subscriber'];

        if (!$settings['enableNotifications']) {
            return;
        }

        if ($comment->getMailsSent()) {
            return;
        }

        $post = $comment->getPost();
        $subscribers = $this->subscriberRepository->findForNotification($post);
        $subject = $this->translate('subject.subscriber.comment.notify', $post->getTitle());
        $variables = [
            'post' => $post,
            'comment' => $comment,
        ];

        $this->signalSlotDispatcher->dispatch(
            self::class,
            'notifySubscribers',
            [$post, &$comment, &$subscribers, &$subject, &$variables, $this]
        );

        $this->getLog()->dev('Send post subscriber notification mails to '.count($subscribers).' users.');

        /* @var $subscriber PostSubscriber */
        foreach ($subscribers as $subscriber) {
            if (empty($subscriber->getEmail())) {
                continue;
            }

            // Make sure we do not notify the author of the triggering comment
            if ($comment->getEmail() === $subscriber->getEmail()) {
                continue;
            }

            $subscriber->updateAuth();
            $this->subscriberRepository->update($subscriber);

            $this->sendSubscriberEmail(
                $subscriber,
                $subject,
                $settings['template']['notification'],
                $variables
            );
        }

        $comment->setMailsSent(true);
        $this->objectManager->get(CommentRepository::class)->update($comment);
    }

    /**
     * Notify the blog admin.
     *
     * @param Comment $comment
     */
    public function notifyAdmin(Comment $comment)
    {
        $settings = $this->subscriptionSettings['admin'];

        if (!$settings['enableNotifications']) {
            return;
        }

        if (!(is_array($settings['mailTo']) && strlen($settings['mailTo']['email']) > 0)) {
            // @extensionScannerIgnoreLine
            $this->getLog()->error('No admin email configured.', $settings['mailTo']);

            return;
        }

        $this->getLog()->dev('Send admin new comment notification mail.');

        /* @var $post Post */
        $post = $comment->getPost();
        $subject = $this->translate('subject.comment.admin.new', $post->getTitle());
        $variables = [
            'post' => $post,
            'languageUid' => $post->getSysLanguageUid(),
            'comment' => $comment,
        ];

        $this->sendEmail(
            [$settings['mailTo']['email'] => $settings['mailTo']['name']],
            $subject,
            $settings['template'],
            $settings,
            $variables
        );
    }
}
