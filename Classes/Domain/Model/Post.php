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

    /**
     * @var bool
     */
    protected bool $hidden = true;

    /**
     * @var bool
     */
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
     * @var int
     */
    protected int $numberOfViews = 0;

    /**
     * If the notification mails are already sent.
     *
     * @var bool
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
    protected ?FileReference $previewImage = null;

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

    /**
     * @inheritDoc
     */
    protected function getPropertiesForSerialization()
    {
        $properties = parent::getPropertiesForSerialization();

        // Remove previewImage due to broken post preview
        // @todo Fix this!
        unset($properties['previewImage']);

        return $properties;
    }

    /**
     * Initializes all Tx_Extbase_Persistence_ObjectStorage properties.
     */
    protected function initStorageObjects()
    {
        $this->categories = new ObjectStorage();
        $this->subscriptions = new ObjectStorage();
        // @extensionScannerIgnoreLine
        $this->content = new ObjectStorage();
    }

    /**
     * @param bool $deleted
     */
    public function setDeleted($deleted)
    {
        $this->deleted = $deleted;
    }

    /**
     * @return bool
     */
    public function getDeleted()
    {
        return $this->deleted;
    }

    /**
     * @param bool $hidden
     */
    public function setHidden($hidden)
    {
        $this->hidden = $hidden;
    }

    /**
     * @return bool
     */
    public function getHidden()
    {
        return $this->hidden;
    }

    /**
     * Returns the title.
     *
     * @return string $title
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Sets the title.
     *
     * @param string $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * Returns the author.
     *
     * @return BackendUser $author
     */
    public function getAuthor()
    {
        if (!($this->author instanceof BackendUser)) {
            $this->author = new BackendUser();
            $this->author->setUserName(uniqid('author-unavailable'));
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
     *
     * @return \DateTime $publishDate
     */
    public function getPublishDate()
    {
        return $this->publishDate;
    }

    /**
     * Returns the publish year.
     *
     * @return string
     */
    public function getPublishYear()
    {
        return $this->publishDate->format('Y');
    }

    /**
     * Returns the publish month.
     *
     * @return string
     */
    public function getPublishMonth()
    {
        return $this->publishDate->format('m');
    }

    /**
     * Returns the publish day.
     *
     * @return string
     */
    public function getPublishDay()
    {
        return $this->publishDate->format('d');
    }

    /**
     * Checks if the post is too old for posting new comments.
     *
     * @param string $expireDate
     *
     * @return string
     */
    public function isExpired($expireDate = '+1 month')
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
     *
     * @return int $allowComments
     */
    public function getAllowComments()
    {
        return $this->allowComments;
    }

    /**
     * Sets the allowComments.
     *
     * @param int $allowComments
     */
    public function setAllowComments($allowComments)
    {
        $this->allowComments = $allowComments;
    }

    /**
     * Returns the tagCloud.
     *
     * @return array $tagCloud
     */
    public function getTagCloud()
    {
        return GeneralUtility::trimExplode(',', $this->tagCloud, true);
    }

    /**
     * Returns the tagCloud as in DB (concated string).
     *
     * @return string
     */
    public function getRawTagCloud()
    {
        return $this->tagCloud;
    }

    /**
     * Sets the tagCloud.
     *
     * @param string $tagCloud
     */
    public function setTagCloud($tagCloud)
    {
        $this->tagCloud = is_array($tagCloud) ? implode(', ', $tagCloud) : $tagCloud;
    }

    /**
     * Returns the numberOfViews.
     *
     * @return int $numberOfViews
     */
    public function getNumberOfViews()
    {
        return $this->numberOfViews;
    }

    /**
     * Sets the numberOfViews.
     *
     * @param int $numberOfViews
     */
    public function setNumberOfViews($numberOfViews)
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

    /**
     * @param bool $mailsSent
     */
    public function setMailsSent($mailsSent)
    {
        $this->mailsSent = (boolean) $mailsSent;
    }

    /**
     * @return bool
     */
    public function getMailsSent()
    {
        return (boolean) $this->mailsSent;
    }

    /**
     * Is it possible to send post subscription mails?
     *
     * @return bool
     */
    public function isMailSendingAllowed()
    {
        return !$this->getMailsSent() && !$this->getHidden() && !$this->getDeleted();
    }

    /**
     * Returns the metaDescription.
     *
     * @return string
     */
    public function getMetaDescription()
    {
        return $this->metaDescription;
    }

    /**
     * Sets the metaDescription.
     *
     * @param string $metaDescription
     */
    public function setMetaDescription($metaDescription)
    {
        $this->metaDescription = $metaDescription;
    }

    /**
     * Returns the metaKeywords.
     *
     * @return string
     */
    public function getMetaKeywords()
    {
        return $this->metaKeywords;
    }

    /**
     * Sets the metaKeywords.
     *
     * @param string $metaKeywords
     */
    public function setMetaKeywords($metaKeywords)
    {
        $this->metaKeywords = $metaKeywords;
    }

    /**
     * Returns the previewMode.
     *
     * @return int
     */
    public function getPreviewMode()
    {
        return $this->previewMode;
    }

    /**
     * Sets the previewMode.
     *
     * @param int $previewMode
     */
    public function setPreviewMode($previewMode)
    {
        $this->previewMode = $previewMode;
    }

    /**
     * Returns the previewText.
     *
     * @return string
     */
    public function getPreviewText()
    {
        return $this->previewText;
    }

    /**
     * Sets the previewText.
     *
     * @param string $previewText
     */
    public function setPreviewText($previewText)
    {
        $this->previewText = $previewText;
    }

    /**
     * Returns the previewImage.
     *
     * @return \TYPO3\CMS\Core\Resource\FileReference|null
     */
    public function getPreviewImage()
    {
        $this->loadLazyRelation($this->previewImage);

        if (!is_object($this->previewImage)) {
            return null;
        }

        return $this->previewImage->getOriginalResource();
    }

    /**
     * Sets the previewImage.
     *
     * @param \TYPO3\CMS\Core\Resource\FileReference $previewImage
     */
    public function setPreviewImage($previewImage)
    {
        $this->previewImage = $previewImage;
    }

    /**
     * Returns the content.
     *
     * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\FelixNagel\T3extblog\Domain\Model\Content> $content
     */
    public function getContent()
    {
        // @extensionScannerIgnoreLine
        return $this->content;
    }

    /**
     * Set content element list.
     *
     * @param ObjectStorage $content content elements
     */
    public function setContent($content)
    {
        // @extensionScannerIgnoreLine
        $this->content = $content;
    }

    /**
     * Adds a content element to the record.
     *
     * @param Content $content
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
     *
     * @return string
     */
    public function getContentIdList()
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
     * all content elements bodytext field values concated without HTML tags
     *
     * @return string
     */
    public function getPreview()
    {
        if ($this->getPreviewText() !== '') {
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
     * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\FelixNagel\T3extblog\Domain\Model\Category> $categories
     */
    public function getCategories()
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
     *
     * @param \FelixNagel\T3extblog\Domain\Model\Comment $comment
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
     *
     * @param \FelixNagel\T3extblog\Domain\Model\Comment $commentToRemove The Comment to be removed
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
     *
     * @return ObjectStorage
     */
    public function getComments()
    {
        $this->initComments();

        return $this->comments;
    }

    /**
     * Returns the comments.
     *
     * @return QueryResultInterface
     */
    public function getCommentsForPaginate()
    {
        $this->initComments();

        return $this->rawComments;
    }

    /**
     * Sets the comments.
     *
     * @param \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\FelixNagel\T3extblog\Domain\Model\Comment> $comments
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
     *
     * @param \FelixNagel\T3extblog\Domain\Model\PostSubscriber $subscriptionToRemove The Subscriber to be removed
     */
    public function removeSubscription(PostSubscriber $subscriptionToRemove)
    {
        $this->subscriptions->detach($subscriptionToRemove);
    }

    /**
     * Returns the subscriptions.
     *
     * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\FelixNagel\T3extblog\Domain\Model\PostSubscriber> $subscriptions
     */
    public function getSubscriptions()
    {
        return $this->subscriptions;
    }

    /**
     * Sets the subscriptions.
     *
     * @param \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\FelixNagel\T3extblog\Domain\Model\PostSubscriber> $subscriptions
     */
    public function setSubscriptions(ObjectStorage $subscriptions)
    {
        $this->subscriptions = $subscriptions;
    }

    /**
     * Returns the permalink configuration.
     *
     * @return array
     */
    public function getLinkParameter()
    {
        return [
            'post' => $this->getUid(),
        ];
    }
}
