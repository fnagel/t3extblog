<?php

namespace FelixNagel\T3extblog\Domain\Model;

/**
 * This file is part of the "t3extblog" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use TYPO3\CMS\Extbase\Annotation\ORM\Lazy;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Persistence\Generic\LazyLoadingProxy;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;
use TYPO3\CMS\Extbase\Persistence\QueryResultInterface;
use TYPO3\CMS\Extbase\Annotation as Extbase;
use TYPO3\CMS\Core\Resource\FileReference;
use TYPO3\CMS\Extbase\Domain\Model\FileReference as ExtbaseFileReference;

/**
 * Post.
 *
 * @SuppressWarnings("PHPMD.ExcessiveClassComplexity")
 * @SuppressWarnings("PHPMD.ExcessivePublicCount")
 * @SuppressWarnings("PHPMD.TooManyFields")
 */
class Post extends AbstractLocalizedEntity
{
    /**
     * @var int
     */
    public const ALLOW_COMMENTS_EVERYONE = 0;

    /**
     * @var int
     */
    public const ALLOW_COMMENTS_NOBODY = 1;

    /**
     * @var int
     */
    public const ALLOW_COMMENTS_LOGIN = 2;

    protected bool $hidden = true;

    protected bool $deleted = false;

    #[Extbase\Validate(['validator' => 'NotEmpty'])]
    protected ?string $title = null;

    protected ?BackendUser $author = null;

    #[Extbase\Validate(['validator' => 'NotEmpty'])]
    protected \DateTime $publishDate;

    protected ?int $allowComments = null;

    /**
     * This var annotation seems needed for Extbase.
     *
     * @var string
     */
    protected string $tagCloud = '';

    protected int $numberOfViews = 0;

    /**
     * If the notification mails are already sent.
     */
    protected bool $mailsSent = false;

    protected ?string $metaDescription = null;

    protected ?string $metaKeywords = null;

    protected ?int $previewMode = null;

    protected ?string $previewText = null;

    #[Lazy]
    protected ExtbaseFileReference|LazyLoadingProxy|null $previewImage = null;

    /**
     * @var ?ObjectStorage<Content>
     */
    #[Lazy]
    protected ?ObjectStorage $content = null;

    /**
     * @var ?ObjectStorage<Category>
     */
    #[Lazy]
    protected ?ObjectStorage $categories = null;

    /**
     * @var ?ObjectStorage<Comment>
     */
    protected ?ObjectStorage $comments = null;

    #[Lazy]
    protected ?QueryResultInterface $rawComments = null;

    /**
     * @var ?ObjectStorage<PostSubscriber>
     */
    #[Lazy]
    protected ?ObjectStorage $subscriptions = null;

    public function __construct()
    {
        $this->initStorageObjects();
    }

    protected function getPropertiesForSerialization(): array
    {
        $properties = parent::getPropertiesForSerialization();

        // Remove previewImage due to broken post preview
        // @todo Preview: Fix this!
        unset($properties['previewImage']);

        return $properties;
    }

    /**
     * Initializes all ObjectStorage properties.
     */
    protected function initStorageObjects()
    {
        $this->categories = new ObjectStorage();
        $this->subscriptions = new ObjectStorage();
        // @extensionScannerIgnoreLine
        $this->content = new ObjectStorage();
    }

    public function setDeleted(bool $deleted): void
    {
        $this->deleted = $deleted;
    }

    public function getDeleted(): bool
    {
        return $this->deleted;
    }

    public function setHidden(bool $hidden): void
    {
        $this->hidden = $hidden;
    }

    public function getHidden(): bool
    {
        return $this->hidden;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    public function getAuthor(): BackendUser
    {
        if (!($this->author instanceof BackendUser)) {
            $this->author = new BackendUser();
            $this->author->setUserName(uniqid('author-unavailable', true));
            $this->author->setRealName('Author unavailable');
        }

        return $this->author;
    }

    public function setAuthor(BackendUser $author): void
    {
        $this->author = $author;
    }

    public function getPublishDate(): \DateTime
    {
        return $this->publishDate;
    }

    public function getPublishYear(): string
    {
        return $this->publishDate->format('Y');
    }

    public function getPublishMonth(): string
    {
        return $this->publishDate->format('m');
    }

    public function getPublishDay(): string
    {
        return $this->publishDate->format('d');
    }

    /**
     * Checks if the post is too old for posting new comments.
     */
    public function isExpired(string $expireDate = '+1 month'): string
    {
        $now = new \DateTime();
        $expire = clone $this->getPublishDate();

        return $now > $expire->modify($expireDate);
    }

    public function setPublishDate(\DateTime $publishDate): void
    {
        $this->publishDate = $publishDate;
    }

    public function getAllowComments(): int
    {
        return $this->allowComments;
    }

    public function setAllowComments(int $allowComments): void
    {
        $this->allowComments = $allowComments;
    }

    public function getTagCloud(): array
    {
        return GeneralUtility::trimExplode(',', $this->tagCloud, true);
    }

    /**
     * Returns the tagCloud as in DB (concated string).
     */
    public function getRawTagCloud(): string
    {
        return $this->tagCloud;
    }

    public function setTagCloud(string|array $tagCloud): void
    {
        $this->tagCloud = is_array($tagCloud) ? implode(', ', $tagCloud) : $tagCloud;
    }

    public function getNumberOfViews(): int
    {
        return $this->numberOfViews;
    }

    public function setNumberOfViews(int $numberOfViews): void
    {
        $this->numberOfViews = $numberOfViews;
    }

    public function riseNumberOfViews()
    {
        ++$this->numberOfViews;
    }

    public function setMailsSent(bool $mailsSent): void
    {
        $this->mailsSent = $mailsSent;
    }

    public function getMailsSent(): bool
    {
        return $this->mailsSent;
    }

    /**
     * Is it possible to send post subscription mails?
     */
    public function isMailSendingAllowed(): bool
    {
        return !$this->getMailsSent() && !$this->getHidden() && !$this->getDeleted();
    }

    public function getMetaDescription(): ?string
    {
        return $this->metaDescription;
    }

    public function setMetaDescription(string $metaDescription): void
    {
        $this->metaDescription = $metaDescription;
    }

    public function getMetaKeywords(): string
    {
        return $this->metaKeywords;
    }

    public function setMetaKeywords(string $metaKeywords): void
    {
        $this->metaKeywords = $metaKeywords;
    }

    public function getPreviewMode(): int
    {
        return $this->previewMode;
    }

    public function setPreviewMode(int $previewMode): void
    {
        $this->previewMode = $previewMode;
    }

    public function getPreviewText(): ?string
    {
        return $this->previewText;
    }

    public function setPreviewText(string $previewText): void
    {
        $this->previewText = $previewText;
    }

    public function getPreviewImage(): ?FileReference
    {
        $this->loadLazyRelation($this->previewImage);

        if (!is_object($this->previewImage)) {
            return null;
        }

        return $this->previewImage->getOriginalResource();
    }

    public function setPreviewImage(ExtbaseFileReference $previewImage): void
    {
        $this->previewImage = $previewImage;
    }

    public function getContent(): ObjectStorage
    {
        // @extensionScannerIgnoreLine
        return $this->content;
    }

    public function setContent(ObjectStorage $content): void
    {
        // @extensionScannerIgnoreLine
        $this->content = $content;
    }

    /**
     * Adds a content element to the record.
     */
    public function addContent(Content $content): void
    {
        if ($this->getContent() === null) {
            // @extensionScannerIgnoreLine
            $this->content = new ObjectStorage();
        }

        // @extensionScannerIgnoreLine
        $this->content->attach($content);
    }

    /**
     * Get UID list of content elements.
     */
    public function getContentIdList(): string
    {
        $idList = [];

        foreach ($this->getContent() as $contentElement) {
            $idList[] = $contentElement->getUid();
        }

        return implode(',', $idList);
    }

    /**
     * Get a plain text only preview of the post.
     *
     * Either using the preview text or
     * all content elements bodytext field values contacted without HTML tags
     */
    public function getPreview(): string
    {
        if ($this->getPreviewText()) {
            return strip_tags($this->getPreviewText());
        }

        $text = [];
        foreach ($this->getContent() as $contentElement) {
            if (strlen($contentElement->getBodytext()) > 0) {
                $text[] = $contentElement->getBodytext();
            }
        }

        return strip_tags(implode('', $text));
    }

    public function addCategory(Category $category): void
    {
        $this->categories->attach($category);
    }

    public function removeCategory(Category $categoryToRemove)
    {
        $this->categories->detach($categoryToRemove);
    }

    public function getCategories(): ObjectStorage
    {
        return $this->categories;
    }

    /**
     * Inits comments.
     *
     * Mapping does not work as relation is not bidirectional, using a repository instead
     * And: its currently not possible to iterate via paginate widget through storage objects
     */
    protected function initComments(): void
    {
        if ($this->comments === null) {
            $this->rawComments = $this->getCommentRepository()->findValidByPost($this);

            $this->comments = new ObjectStorage();
            foreach ($this->rawComments as $comment) {
                $this->comments->attach($comment);
            }
        }
    }

    public function addComment(Comment $comment): void
    {
        $this->initComments();

        $comment->setPostId($this->getLocalizedUid());

        $this->comments->attach($comment);
        $this->getCommentRepository()->add($comment);
    }

    public function removeComment(Comment $commentToRemove): void
    {
        $this->initComments();

        $commentToRemove->setDeleted(true);

        $this->comments->detach($commentToRemove);
        $this->getCommentRepository()->update($commentToRemove);
    }

    public function getComments(): ObjectStorage
    {
        $this->initComments();

        return $this->comments;
    }

    public function getCommentsForPaginate(): QueryResultInterface
    {
        $this->initComments();

        return $this->rawComments;
    }

    public function setComments(ObjectStorage $comments): void
    {
        $this->comments = $comments;
    }

    public function addSubscription(PostSubscriber $subscription): void
    {
        $this->subscriptions->attach($subscription);
    }

    public function removeSubscription(PostSubscriber $subscriptionToRemove): void
    {
        $this->subscriptions->detach($subscriptionToRemove);
    }

    public function getSubscriptions(): ?ObjectStorage
    {
        return $this->subscriptions;
    }

    public function setSubscriptions(ObjectStorage $subscriptions): void
    {
        $this->subscriptions = $subscriptions;
    }

    /**
     * Returns the permalink configuration.
     */
    public function getLinkParameter(): array
    {
        return [
            'post' => $this->getUid(),
        ];
    }
}
