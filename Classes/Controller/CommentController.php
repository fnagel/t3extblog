<?php

namespace FelixNagel\T3extblog\Controller;

/**
 * This file is part of the "t3extblog" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use TYPO3\CMS\Extbase\Annotation\IgnoreValidation;
use FelixNagel\T3extblog\Validation\Validator\CommentEmailValidator;
use FelixNagel\T3extblog\Validation\Validator\PrivacyPolicyValidator;
use FelixNagel\T3extblog\Event;
use TYPO3\CMS\Core\Type\ContextualFeedbackSeverity as Message;
use FelixNagel\T3extblog\Domain\Repository\CommentRepository;
use FelixNagel\T3extblog\Service\CommentNotificationService;
use FelixNagel\T3extblog\Service\SpamCheckServiceInterface;
use FelixNagel\T3extblog\Utility\FrontendUtility;
use FelixNagel\T3extblog\Domain\Model\Comment;
use FelixNagel\T3extblog\Domain\Model\Post;
use TYPO3\CMS\Extbase\Annotation as Extbase;
use Psr\Http\Message\ResponseInterface;

/**
 * CommentController.
 */
class CommentController extends AbstractCommentController
{
    /**
     * CommentController constructor.
     */
    public function __construct(
        protected CommentRepository $commentRepository,
        protected CommentNotificationService $notificationService,
        protected SpamCheckServiceInterface $spamCheckService
    ) {
    }

    /**
     * action list.
     *
     * @param Post|null $post Show only comments related to this post
     */
    public function listAction(int $page = 1, Post $post = null): ResponseInterface
    {
        if ($post === null) {
            $comments = $this->commentRepository->findValid();
        } else {
            $comments = $this->commentRepository->findValidByPost($post);
            $this->view->assign('post', $post);
        }

        // Add basic PID based cache tag
        // @extensionScannerIgnoreLine
        $this->addCacheTags($comments->getFirst());

        $this->view->assign('comments', $comments);

        return $this->paginationHtmlResponse(
            $comments,
            $this->settings['latestComments']['paginate'],
            $page
        );
    }

    /**
     * action latest.
     *
     * @param Post|null $post Show only comments related to this post
     */
    public function latestAction(int $page = 1, Post $post = null): ResponseInterface
    {
        return $this->listAction($page, $post);
    }

    /**
     * Show action.
     *
     * Redirect to post show if empty comment create is called
     *
     * @param Post $post The post the comment is related to
     */
    public function showAction(Post $post): ResponseInterface
    {
        return $this->redirect('show', 'Post', null, $post->getLinkParameter());
    }

    /**
     * action new.
     *
     * @param Post $post The post the comment is related to
     */
    #[IgnoreValidation(['value' => 'newComment'])]
    public function newAction(Post $post, Comment $newComment = null): ResponseInterface
    {
        if ($newComment === null) {
            $newComment = $this->getNewComment();
        }

        $this->view->assign('newComment', $newComment);
        $this->view->assign('post', $post);

        return $this->htmlResponse();
    }

    public function initializeCreateAction()
    {
        $settings = $this->settings['blogsystem']['comments']['rateLimit'];

        if ($settings['enable']) {
            $this->initRateLimiter('comment-create', $settings);
        }
    }

    /**
     * Adds a comment to a blog post and redirects to single view.
     *
     * @param Post    $post       The post the comment is related to
     * @param Comment $newComment The comment to create
     */
    #[Extbase\Validate(['validator' => CommentEmailValidator::class, 'param' => 'newComment'])]
    #[Extbase\Validate(['validator' => PrivacyPolicyValidator::class, 'param' => 'newComment', 'options' => ['key' => 'comment', 'property' => 'privacyPolicyAccepted']])]
    public function createAction(Post $post, Comment $newComment = null): ResponseInterface
    {
        // @todo Fix flash messages caching issue, see: https://github.com/fnagel/t3extblog/issues/112
        $this->clearPageCache();

        if (($rateLimitResult = $this->checkRateLimit()) instanceof ResponseInterface) {
            return $rateLimitResult;
        }

        if ($newComment === null) {
            $this->addFlashMessageByKey('noComment', Message::WARNING);
            return $this->redirect('show', 'Post', null, $post->getLinkParameter());
        }

        if (($commentAllowedResult = $this->checkIfCommentIsAllowed($post)) instanceof ResponseInterface) {
            return $commentAllowedResult;
        }

        if (($spamPointResult = $this->checkSpamPoints($newComment)) instanceof ResponseInterface) {
            return $spamPointResult;
        }

        $this->sanitizeComment($newComment);

        if ($this->settings['blogsystem']['comments']['approvedByDefault']) {
            $newComment->setApproved(true);
        }

        /** @var Event\Comment\CreatePrePersistEvent $event */
        $event = $this->eventDispatcher->dispatch(
            new Event\Comment\CreatePrePersistEvent($post, $newComment)
        );
        $post = $event->getPost();
        $newComment = $event->getComment();

        $post->addComment($newComment);
        $this->persistAllEntities();

        // Process comment (send mails, clear cache, etc.)
        $this->notificationService->processNewEntity($newComment);
        $this->notificationService->notifyAdmin($newComment);
        $this->notificationService->flushFrontendCache($newComment);

        if (!$this->hasFlashMessages()) {
            if ($newComment->isApproved()) {
                $this->addFlashMessageByKey('created');
            } else {
                $this->addFlashMessageByKey('createdDisapproved', Message::INFO);
            }
        }

        $this->getRateLimiter()->reset('comment-create');

        return $this->redirect('show', 'Post', null, $post->getLinkParameter());
    }

    /**
     * Checks rate limit for request.
     */
    protected function checkRateLimit(): ?ResponseInterface
    {
        $settings = $this->settings['blogsystem']['comments']['rateLimit'];

        if ($settings['enable'] && !$this->getRateLimiter()->isAccepted('comment-create')) {
            $this->addFlashMessageByKey('rateLimit', Message::ERROR);
            return $this->errorAction();
        }

        return null;
    }

    /**
     * Checks if a new comment could be created.
     *
     * @param Post $post The post the comment is related to
     */
    protected function checkIfCommentIsAllowed(Post $post): ?ResponseInterface
    {
        $settings = $this->settings['blogsystem']['comments'];

        if (!$settings['allowed'] || $post->getAllowComments() === Post::ALLOW_COMMENTS_NOBODY) {
            $this->addFlashMessageByKey('notAllowed', Message::ERROR);
            return $this->errorAction();
        }

        if ($post->getAllowComments() === Post::ALLOW_COMMENTS_LOGIN && !FrontendUtility::isUserLoggedIn()) {
            $this->addFlashMessageByKey('notLoggedIn', Message::ERROR);
            return $this->errorAction();
        }

        if ($settings['allowedUntil'] && $post->isExpired(trim($settings['allowedUntil']))) {
            $this->addFlashMessageByKey('commentsClosed', Message::ERROR);
            return $this->errorAction();
        }

        return null;
    }

    protected function checkSpamPoints(Comment $comment): ?ResponseInterface
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
            return $this->redirect(
                '',
                null,
                null,
                $settings['redirect']['arguments'] ?? null,
                (int)$settings['redirect']['pid']
            );
        }

        // block comment and show message
        if ($threshold['block'] > 0 && $comment->getSpamPoints() >= (int) $threshold['block']) {
            $this->getLog()->notice('New comment blocked because of SPAM.', $logData);
            $this->addFlashMessageByKey('blockedAsSpam', Message::ERROR);
            return $this->errorAction();
        }

        // mark as spam
        if ($comment->getSpamPoints() >= (int) $threshold['markAsSpam']) {
            $this->getLog()->notice('New comment marked as SPAM.', $logData);
            $comment->markAsSpam();
            $this->addFlashMessageByKey('markedAsSpam', Message::INFO);
        }

        return null;
    }

    /**
     * Sanitize comment content.
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
    protected function getErrorFlashMessage(): bool
    {
        return false;
    }
}
