<?php

namespace FelixNagel\T3extblog\Controller;

/**
 * This file is part of the "t3extblog" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use FelixNagel\T3extblog\Domain\Repository\CommentRepository;
use FelixNagel\T3extblog\Service\CommentNotificationService;
use FelixNagel\T3extblog\Service\SpamCheckServiceInterface;
use FelixNagel\T3extblog\Utility\GeneralUtility;
use TYPO3\CMS\Core\Messaging\FlashMessage;
use FelixNagel\T3extblog\Domain\Model\Comment;
use FelixNagel\T3extblog\Domain\Model\Post;
use TYPO3\CMS\Extbase\Annotation as Extbase;
use Psr\Http\Message\ResponseInterface;

/**
 * CommentController.
 */
class CommentController extends AbstractCommentController
{
    protected array $cHashActions = [
        'listAction',
        'latestAction',
        'showAction',
    ];

    /**
     * commentRepository.
     */
    protected CommentRepository $commentRepository;

    /**
     * Notification Service.
     */
    protected CommentNotificationService $notificationService;

    /**
     * Spam Check Service.
     */
    protected SpamCheckServiceInterface $spamCheckService;

    /**
     * CommentController constructor.
     *
     */
    public function __construct(
        CommentRepository $commentRepository,
        CommentNotificationService $notificationService,
        SpamCheckServiceInterface $spamCheckService
    ) {
        $this->commentRepository = $commentRepository;
        $this->notificationService = $notificationService;
        $this->spamCheckService = $spamCheckService;
    }

    /**
     * action list.
     *
     * @param Post|null $post Show only comments related to this post
     */
    public function listAction(Post $post = null): ResponseInterface
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

        return $this->htmlResponse();
    }

    /**
     * action latest.
     *
     * @param Post|null $post Show only comments related to this post
     */
    public function latestAction(Post $post = null): ResponseInterface
    {
        $this->listAction($post);

        return $this->htmlResponse();
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
     * @TYPO3\CMS\Extbase\Annotation\IgnoreValidation("newComment")
     */
    public function newAction(Post $post, Comment $newComment = null): ResponseInterface
    {
        if ($newComment === null) {
            $newComment = $this->getNewComment();
        }

        $this->view->assign('newComment', $newComment);
        $this->view->assign('post', $post);

        return $this->htmlResponse();
    }

    /**
     * Adds a comment to a blog post and redirects to single view.
     *
     * @param Post    $post       The post the comment is related to
     * @param Comment $newComment The comment to create
     * @Extbase\Validate("\FelixNagel\T3extblog\Validation\Validator\CommentEmailValidator", param="newComment")
     * @Extbase\Validate("\FelixNagel\T3extblog\Validation\Validator\PrivacyPolicyValidator", param="newComment", options={"key": "comment", "property": "privacyPolicyAccepted"})
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
            self::class,
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
     * @inheritDoc
     */
    protected function clearCacheOnError()
    {
        $this->clearPageCache();
    }

    /**
     * Checks if a new comment could be created.
     *
     * @param Post $post The post the comment is related to
     */
    protected function checkIfCommentIsAllowed(Post $post)
    {
        $settings = $this->settings['blogsystem']['comments'];

        if (!$settings['allowed'] || $post->getAllowComments() === Post::ALLOW_COMMENTS_NOBODY) {
            $this->addFlashMessageByKey('notAllowed', FlashMessage::ERROR);
            $this->errorAction();
        }

        if ($post->getAllowComments() === Post::ALLOW_COMMENTS_LOGIN && !GeneralUtility::isUserLoggedIn()
        ) {
            $this->addFlashMessageByKey('notLoggedIn', FlashMessage::ERROR);
            $this->errorAction();
        }

        if ($settings['allowedUntil'] && $post->isExpired(trim($settings['allowedUntil']))) {
            $this->addFlashMessageByKey('commentsClosed', FlashMessage::ERROR);
            $this->errorAction();
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
        if ($threshold['redirect'] > 0 && $comment->getSpamPoints() >= (int) $threshold['redirect']) {
            $this->getLog()->notice('New comment blocked and user redirected because of SPAM.', $logData);
            $this->redirect(
                '',
                null,
                null,
                $settings['redirect']['arguments'],
                (int)$settings['redirect']['pid'],
                0,
                403
            );
        }

        // block comment and show message
        if ($threshold['block'] > 0 && $comment->getSpamPoints() >= (int) $threshold['block']) {
            $this->getLog()->notice('New comment blocked because of SPAM.', $logData);
            $this->addFlashMessageByKey('blockedAsSpam', FlashMessage::ERROR);
            $this->errorAction();
        }

        // mark as spam
        if ($comment->getSpamPoints() >= (int) $threshold['markAsSpam']) {
            $this->getLog()->notice('New comment marked as SPAM.', $logData);
            $comment->markAsSpam();
            $this->addFlashMessageByKey('markedAsSpam', FlashMessage::NOTICE);
        }
    }

    /**
     * Sanitize comment content.
     *
     */
    protected function sanitizeComment(Comment $comment)
    {
        // Remove non tag chars
        $allowedTags = preg_replace('#[^\w<>]#i', '', $this->settings['blogsystem']['comments']['allowTags']);
        // Remove bad tags
        $allowedTags = preg_replace('#<(script|link|i?frame)>#i', '', $allowedTags);

        // Remove unwanted tags from text
        $text = strip_tags($comment->getText(), $allowedTags);

        // Remove all attributes
        $text = preg_replace('#<([a-z][a-z0-9]*)[^>]*?(\/?)>#i', '<$1$2>', $text);

        $comment->setText($text);
    }

    /**
     * Disable error flash message.
     */
    protected function getErrorFlashMessage(): string
    {
        return false;
    }
}
