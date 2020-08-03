<?php

namespace FelixNagel\T3extblog\Controller;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2013-2020 Felix Nagel <info@felixnagel.com>
 *
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

use TYPO3\CMS\Core\Messaging\FlashMessage;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use FelixNagel\T3extblog\Domain\Model\Comment;
use FelixNagel\T3extblog\Domain\Model\Post;

/**
 * CommentController.
 */
class CommentController extends AbstractController
{
    /**
     * @var array
     */
    protected $cHashActions = [
        'listAction',
        'latestAction',
        'showAction',
    ];

    /**
     * commentRepository.
     *
     * @var \FelixNagel\T3extblog\Domain\Repository\CommentRepository
     * @inject
     */
    protected $commentRepository;

    /**
     * Notification Service.
     *
     * @var \FelixNagel\T3extblog\Service\CommentNotificationService
     * @inject
     */
    protected $notificationService;

    /**
     * Spam Check Service.
     *
     * @var \FelixNagel\T3extblog\Service\SpamCheckServiceInterface
     * @inject
     */
    protected $spamCheckService;

    /**
     * @var \FelixNagel\T3extblog\Service\FlushCacheService
     * @inject
     */
    protected $cacheService;

    /**
     * action list.
     *
     * @param Post $post Show only comments related to this post
     */
    public function listAction(Post $post = null)
    {
        if ($post === null) {
            $comments = $this->commentRepository->findValid();
        } else {
            $comments = $this->commentRepository->findValidByPost($post);
            $this->view->assign('post', $post);
        }

        // Add basic PID based cache tag
        $this->addCacheTags($comments->getFirst());

        $this->view->assign('comments', $comments);
    }

    /**
     * action latest.
     *
     * @param Post $post Show only comments related to this post
     */
    public function latestAction(Post $post = null)
    {
        $this->listAction($post);
    }

    /**
     * Show action.
     *
     * Redirect to post show if empty comment create is called
     *
     * @param Post $post The post the comment is related to
     */
    public function showAction(Post $post)
    {
        $this->redirect('show', 'Post', null, $post->getLinkParameter());
    }

    /**
     * action new.
     *
     * @param Post    $post       The post the comment is related to
     * @param Comment $newComment
     * @ignorevalidation $newComment
     */
    public function newAction(Post $post, Comment $newComment = null)
    {
        if ($newComment === null) {
            $newComment = $this->objectManager->get(Comment::class);
        }

        $this->view->assign('newComment', $newComment);
        $this->view->assign('post', $post);
    }

    /**
     * Adds a comment to a blog post and redirects to single view.
     *
     * @param Post    $post       The post the comment is related to
     * @param Comment $newComment The comment to create
     * @validate $newComment \FelixNagel\T3extblog\Validation\Validator\PrivacyPolicyValidator(key='comment', property='privacyPolicyAccepted')
     */
    public function createAction(Post $post, Comment $newComment)
    {
        $this->checkIfCommentIsAllowed($post);
        $this->checkSpamPoints($newComment);
        $this->sanitizeComment($newComment);

        if ($this->settings['blogsystem']['comments']['approvedByDefault']) {
            $newComment->setApproved(true);
        }

        $this->signalSlotDispatcher->dispatch(
            __CLASS__,
            'prePersist',
            [$post, &$newComment, $this]
        );

        $post->addComment($newComment);
        $this->persistAllEntities();

        // Process comment (send mails, clear cache, etc.)
        $this->notificationService->processNewEntity($newComment);
        $this->notificationService->notifyAdmin($newComment);
        $this->notificationService->flushFrontendCache($newComment);

        if (!$this->hasFlashMessages()) {
            if ($newComment->isApproved()) {
                $this->addFlashMessageByKey('created', FlashMessage::OK);
            } else {
                $this->addFlashMessageByKey('createdDisapproved', FlashMessage::NOTICE);
            }
        }

        $this->redirect('show', 'Post', null, $post->getLinkParameter());
    }

    /**
     * Clear cache of current post page and sends correct header.
     */
    protected function clearCacheOnError()
    {
        if ($this->arguments->hasArgument('post')) {
            $post = $this->arguments->getArgument('post')->getValue();
            $this->cacheService->addCacheTagsToFlush([
                'tx_t3blog_post_uid_'.$post->getLocalizedUid(),
            ]);
        } else {
            parent::clearCacheOnError();
        }

        $this->response->setHeader('Cache-Control', 'private', true);
        $this->response->setHeader('Expires', '0', true);
        $this->response->setHeader('Pragma', 'no-cache', true);
        $this->response->sendHeaders();
    }

    /**
     * Checks if a new comment could be created.
     *
     * @param Post $post The post the comment is related to
     */
    protected function checkIfCommentIsAllowed(Post $post)
    {
        $settings = $this->settings['blogsystem']['comments'];

        if (!$settings['allowed'] || $post->getAllowComments() === 1) {
            $this->addFlashMessageByKey('notAllowed', FlashMessage::ERROR);
            $this->errorAction();
        }

        if ($post->getAllowComments() === 2 && empty(\FelixNagel\T3extblog\Utility\GeneralUtility::getTsFe()->loginUser)) {
            $this->addFlashMessageByKey('notLoggedIn', FlashMessage::ERROR);
            $this->errorAction();
        }

        if ($settings['allowedUntil']) {
            if ($post->isExpired(trim($settings['allowedUntil']))) {
                $this->addFlashMessageByKey('commentsClosed', FlashMessage::ERROR);
                $this->errorAction();
            }
        }
    }

    /**
     * Process comment request.
     *
     * @param Comment $comment The comment to be deleted
     */
    protected function checkSpamPoints(Comment $comment)
    {
        $settings = $this->settings['blogsystem']['comments']['spamCheck'];
        $comment->setSpamPoints($this->spamCheckService->process($settings));

        $threshold = $settings['threshold'];
        $logData = [
            'commentUid' => $comment->getUid(),
            'spamPoints' => $comment->getSpamPoints(),
        ];

        // block comment and redirect user
        if ($threshold['redirect'] > 0 && $comment->getSpamPoints() >= intval($threshold['redirect'])) {
            $this->log->notice('New comment blocked and user redirected because of SPAM.', $logData);
            $this->redirect(
                '',
                null,
                null,
                $settings['redirect']['arguments'],
                intval($settings['redirect']['pid']),
                0,
                $statusCode = 403
            );
        }

        // block comment and show message
        if ($threshold['block'] > 0 && $comment->getSpamPoints() >= intval($threshold['block'])) {
            $this->log->notice('New comment blocked because of SPAM.', $logData);
            $this->addFlashMessageByKey('blockedAsSpam', FlashMessage::ERROR);
            $this->errorAction();
        }

        // mark as spam
        if ($comment->getSpamPoints() >= intval($threshold['markAsSpam'])) {
            $this->log->notice('New comment marked as SPAM.', $logData);
            $comment->markAsSpam();
            $this->addFlashMessageByKey('markedAsSpam', FlashMessage::NOTICE);
        }
    }

    /**
     * Sanitize comment content.
     *
     * @param Comment $comment
     */
    protected function sanitizeComment(Comment $comment)
    {
        // Remove non tag chars
        $allowedTags = preg_replace('/[^\w<>]/i', '', $this->settings['blogsystem']['comments']['allowTags']);
        // Remove bad tags
        $allowedTags = preg_replace('/<(script|link|i?frame)>/i', '', $allowedTags);

        // Remove unwanted tags from text
        $text = strip_tags($comment->getText(), $allowedTags);

        if ($this->settings['blogsystem']['comments']['allowSomeTagAttributes']) {
            $text = GeneralUtility::removeXSS($text);
        } else {
            // Remove all attributes
            $text = preg_replace('/<([a-z][a-z0-9]*)[^>]*?(\/?)>/i', '<$1$2>', $text);
        }

        $comment->setText($text);
    }

    /**
     * Disable error flash message.
     *
     * @return string|bool
     */
    protected function getErrorFlashMessage()
    {
        return false;
    }
}
