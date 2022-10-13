<?php

namespace FelixNagel\T3extblog\Domain\Model;

/**
 * This file is part of the "t3extblog" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;
use TYPO3\CMS\Extbase\Persistence\QueryResultInterface;
use TYPO3\CMS\Extbase\Annotation as Extbase;
use TYPO3\CMS\Core\Resource\FileReference;

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

    /**
     * title.
     *
     * @var string
     * @Extbase\Validate("NotEmpty")
     */
    protected ?string $title = null;

    /**
     * author.
     *
     * @var \FelixNagel\T3extblog\Domain\Model\BackendUser
     */
    protected $author;

    /**
     * publishDate.
     *
     * @var \DateTime
     * @Extbase\Validate("NotEmpty")
     */
    protected $publishDate;

    /**
     * allowComments.
     *
     * @var int
     */
    protected ?int $allowComments = null;

    /**
     * tagCloud.
     *
     * @var string
     */
    protected ?string $tagCloud = null;

    /**
     * numberOfViews.
     *
     */
    protected int $numberOfViews = 0;

    /**
     * If the notification mails are already sent.
     *
     */
    protected bool $mailsSent = false;

    /**
     * metaDescription.
     *
     * @var string
     */
    protected ?string $metaDescription = null;

    /**
     * metaKeywords.
     *
     * @var string
     */
    protected ?string $metaKeywords = null;

    /**
     * previewMode.
     *
     * @var int
     */
    protected ?int $previewMode = null;

    /**
     * previewText.
     *
     * @var string
     */
    protected ?string $previewText = null;

    /**
     * previewImage.
     *
     * @var \TYPO3\CMS\Extbase\Domain\Model\FileReference
     * @TYPO3\CMS\Extbase\Annotation\ORM\Lazy
     */
    protected $previewImage = null;

    /**
     * content.
     *
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\FelixNagel\T3extblog\Domain\Model\Content>
     * @TYPO3\CMS\Extbase\Annotation\ORM\Lazy
     */
    protected ?ObjectStorage $content = null;

    /**
     * categories.
     *
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\FelixNagel\T3extblog\Domain\Model\Category>
     * @TYPO3\CMS\Extbase\Annotation\ORM\Lazy
     */
    protected ?ObjectStorage $categories = null;

    /**
     * comments.
     *
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\FelixNagel\T3extblog\Domain\Model\Comment>
     */
    protected ?ObjectStorage $comments = null;

    /**
     * raw comments.
     *
     * @var QueryResultInterface
     * @TYPO3\CMS\Extbase\Annotation\ORM\Lazy
     */
    protected $rawComments = null;

    /**
     * subscriptions.
     *
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\FelixNagel\T3extblog\Domain\Model\PostSubscriber>
     * @TYPO3\CMS\Extbase\Annotation\ORM\Lazy
     */
    protected ?ObjectStorage $subscriptions = null;

    /**
     * __construct.
     */
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

    public function setDeleted(bool $deleted)
    {
        $this->deleted = $deleted;
    }

    public function getDeleted(): bool
    {
        return $this->deleted;
    }

    public function setHidden(bool $hidden)
    {
        $this->hidden = $hidden;
    }

    public function getHidden(): bool
    {
        return $this->hidden;
    }

    /**
     * Returns the title.
     *
     * @return string $title
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * Sets the title.
     *
     */
    public function setTitle(string $title)
    {
        $this->title = $title;
    }

    /**
     * Returns the author.
     *
     * @return BackendUser $author
     */
    public function getAuthor(): BackendUser
    {
        if (!($this->author instanceof BackendUser)) {
            $this->author = new BackendUser();
            $this->author->setUserName(uniqid('author-unavailable', true));
            $this->author->setRealName('Author unavailable');
        }

        return $this->author;
    }

    /**
     * Sets the author.
     *
     * @param BackendUser|int $author
     */
    public function setAuthor($author)
    {
        if ($author instanceof BackendUser) {
            $this->author = $author->getUid();
        } elseif ((int) $author !== 0) {
            $this->author = (int)$author;
        }
    }

    /**
     * Returns the publishDate.
     */
    public function getPublishDate(): \DateTime
    {
        return $this->publishDate;
    }

    /**
     * Returns the publish year.
     */
    public function getPublishYear(): string
    {
        return $this->publishDate->format('Y');
    }

    /**
     * Returns the publish month.
     */
    public function getPublishMonth(): string
    {
        return $this->publishDate->format('m');
    }

    /**
     * Returns the publish day.
     */
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

    /**
     * Sets the publishDate.
     *
     * @param \DateTime|\DateTimeImmutable $publishDate
     */
    public function setPublishDate(\DateTimeInterface $publishDate)
    {
        $this->publishDate = $publishDate;
    }

    /**
     * Returns the allowComments.
     */
    public function getAllowComments(): int
    {
        return $this->allowComments;
    }

    /**
     * Sets the allowComments.
     */
    public function setAllowComments(int $allowComments)
    {
        $this->allowComments = $allowComments;
    }

    /**
     * Returns the tagCloud.
     */
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

    /**
     * Sets the tagCloud.
     *
     * @param string|array $tagCloud
     */
    public function setTagCloud($tagCloud)
    {
        $this->tagCloud = is_array($tagCloud) ? implode(', ', $tagCloud) : $tagCloud;
    }

    /**
     * Returns the numberOfViews.
     */
    public function getNumberOfViews(): int
    {
        return $this->numberOfViews;
    }

    /**
     * Sets the numberOfViews.
     */
    public function setNumberOfViews(int $numberOfViews)
    {
        $this->numberOfViews = $numberOfViews;
    }

    /**
     * Rise the numberOfViews.
     */
    public function riseNumberOfViews()
    {
        ++$this->numberOfViews;
    }

    public function setMailsSent(bool $mailsSent)
    {
        $this->mailsSent = $mailsSent;
    }


    public function getMailsSent(): bool
    {
        return $this->mailsSent;
    }

    /**
     * Is it possible to send post subscription mails?
     *
     */
    public function isMailSendingAllowed(): bool
    {
        return !$this->getMailsSent() && !$this->getHidden() && !$this->getDeleted();
    }

    /**
     * Returns the metaDescription.
     */
    public function getMetaDescription(): ?string
    {
        return $this->metaDescription;
    }

    /**
     * Sets the metaDescription.
     */
    public function setMetaDescription(string $metaDescription)
    {
        $this->metaDescription = $metaDescription;
    }

    /**
     * Returns the metaKeywords.
     */
    public function getMetaKeywords(): string
    {
        return $this->metaKeywords;
    }

    /**
     * Sets the metaKeywords.
     */
    public function setMetaKeywords(string $metaKeywords)
    {
        $this->metaKeywords = $metaKeywords;
    }

    /**
     * Returns the previewMode.
     */
    public function getPreviewMode(): int
    {
        return $this->previewMode;
    }

    /**
     * Sets the previewMode.
     */
    public function setPreviewMode(int $previewMode)
    {
        $this->previewMode = $previewMode;
    }

    /**
     * Returns the previewText.
     */
    public function getPreviewText(): ?string
    {
        return $this->previewText;
    }

    /**
     * Sets the previewText.
     */
    public function setPreviewText(string $previewText)
    {
        $this->previewText = $previewText;
    }

    /**
     * Returns the previewImage.
     */
    public function getPreviewImage(): ?FileReference
    {
        $this->loadLazyRelation($this->previewImage);

        if (!is_object($this->previewImage)) {
            return null;
        }

        return $this->previewImage->getOriginalResource();
    }

    /**
     * Sets the previewImage.
     */
    public function setPreviewImage(FileReference $previewImage)
    {
        $this->previewImage = $previewImage;
    }

    /**
     * Returns the content.
     *
     * @return ObjectStorage $content
     */
    public function getContent(): ObjectStorage
    {
        // @extensionScannerIgnoreLine
        return $this->content;
    }

    /**
     * Set content element list.
     *
     * @param ObjectStorage $content content elements
     */
    public function setContent(ObjectStorage $content)
    {
        // @extensionScannerIgnoreLine
        $this->content = $content;
    }

    /**
     * Adds a content element to the record.
     */
    public function addContent(Content $content)
    {
        if ($this->getContent() === null) {
            // @extensionScannerIgnoreLine
            $this->content = new ObjectStorage();
        }

        // @extensionScannerIgnoreLine
        $this->content->attach($content);
    }

    /**
     * Get id list of content elements.
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

    /**
     * Adds a Category.
     *
     * @param \FelixNagel\T3extblog\Domain\Model\Category $category
     */
    public function addCategory(Category $category)
    {
        $this->categories->attach($category);
    }

    /**
     * Removes a Category.
     *
     * @param \FelixNagel\T3extblog\Domain\Model\Category $categoryToRemove The Category to be removed
     */
    public function removeCategory(Category $categoryToRemove)
    {
        $this->categories->detach($categoryToRemove);
    }

    /**
     * Returns the categories.
     *
     * @return ObjectStorage $categories
     */
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
    protected function initComments()
    {
        if ($this->comments === null) {
            $this->rawComments = $this->getCommentRepository()->findValidByPost($this);

            $this->comments = new ObjectStorage();
            foreach ($this->rawComments as $comment) {
                $this->comments->attach($comment);
            }
        }
    }

    /**
     * Adds a Comment.
     */
    public function addComment(Comment $comment)
    {
        $this->initComments();

        $comment->setPostId($this->getLocalizedUid());

        $this->comments->attach($comment);
        $this->getCommentRepository()->add($comment);
    }

    /**
     * Removes a Comment.
     */
    public function removeComment(Comment $commentToRemove)
    {
        $this->initComments();

        $commentToRemove->setDeleted(true);

        $this->comments->detach($commentToRemove);
        $this->getCommentRepository()->update($commentToRemove);
    }

    /**
     * Returns the comments.
     */
    public function getComments(): ObjectStorage
    {
        $this->initComments();

        return $this->comments;
    }

    /**
     * Returns the comments.
     */
    public function getCommentsForPaginate(): QueryResultInterface
    {
        $this->initComments();

        return $this->rawComments;
    }

    /**
     * Sets the comments.
     */
    public function setComments(ObjectStorage $comments)
    {
        $this->comments = $comments;
    }

    /**
     * Adds a Subscriber.
     *
     * @param \FelixNagel\T3extblog\Domain\Model\PostSubscriber $subscription
     */
    public function addSubscription(PostSubscriber $subscription)
    {
        $this->subscriptions->attach($subscription);
    }

    /**
     * Removes a Subscriber.
     */
    public function removeSubscription(PostSubscriber $subscriptionToRemove)
    {
        $this->subscriptions->detach($subscriptionToRemove);
    }

    /**
     * Returns the subscriptions.
     */
    public function getSubscriptions(): ?ObjectStorage
    {
        return $this->subscriptions;
    }

    /**
     * Sets the subscriptions.
     */
    public function setSubscriptions(ObjectStorage $subscriptions)
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
