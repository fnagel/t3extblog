<?php

namespace FelixNagel\T3extblog\Domain\Model;

/**
 * This file is part of the "t3extblog" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use TYPO3\CMS\Extbase\Annotation as Extbase;

/**
 * Comment.
 *
 * @SuppressWarnings("PHPMD.TooManyFields")
 */
class Comment extends AbstractEntity
{
    protected bool $hidden = false;


    protected bool $deleted = false;

    /**
     * title.
     *
     * @Extbase\Validate("Text")
     *
     * @var string
     */
    protected ?string $title = null;

    /**
     * author.
     *
     * @Extbase\Validate("NotEmpty")
     * @Extbase\Validate("Text")
     *
     * @var string
     */
    protected ?string $author = null;

    /**
     * email.
     *
     * @var string
     * @Extbase\Validate("EmailAddress")
     */
    protected ?string $email = null;

    /**
     * website.
     *
     * @var string
     * @Extbase\Validate("\FelixNagel\T3extblog\Validation\Validator\UrlValidator")
     */
    protected ?string $website = null;

    /**
     * date.
     *
     * @var \DateTime
     */
    protected $date;

    /**
     * text.
     *
     * @var string
     * @Extbase\Validate("NotEmpty")
     */
    protected ?string $text = null;

    /**
     * approved.
     *
     */
    protected bool $approved = false;

    /**
     * spam.
     *
     */
    protected bool $spam = false;

    /**
     * spamPoints.
     *
     * @var int
     */
    protected ?int $spamPoints = null;

    /**
     * postId.
     *
     * @var int
     */
    protected ?int $postId = null;

    /**
     * post.
     *
     * @var \FelixNagel\T3extblog\Domain\Model\Post
     * @TYPO3\CMS\Extbase\Annotation\ORM\Lazy
     */
    protected ?Post $post = null;

    /**
     * subscribe (not persisted).
     *
     */
    protected bool $subscribe = false;

    /**
     * If the notification mails are already sent.
     *
     */
    protected bool $mailsSent = false;

    /**
     * privacy policy accepted.
     *
     */
    protected bool $privacyPolicyAccepted = false;

    /**
     * __construct.
     */
    public function __construct()
    {
        $this->date = new \DateTime();
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
     * @return string $author
     */
    public function getAuthor(): string
    {
        return $this->author;
    }

    /**
     * Sets the author.
     *
     */
    public function setAuthor(string $author)
    {
        $this->author = $author;
    }

    /**
     * Returns the email.
     *
     * @return string $email
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * Sets the email.
     *
     */
    public function setEmail(string $email)
    {
        $this->email = $email;
    }

    /**
     * Returns the website.
     *
     * @return string $website
     */
    public function getWebsite(): string
    {
        return $this->website;
    }

    /**
     * Sets the website.
     *
     */
    public function setWebsite(string $website)
    {
        $this->website = $website;
    }

    /**
     * Returns the date.
     *
     * @return \DateTime $date
     */
    public function getDate(): \DateTime
    {
        return $this->date;
    }

    /**
     * Sets the date.
     *
     * @param \DateTime|\DateTimeImmutable $date
     */
    public function setDate(\DateTimeInterface $date)
    {
        $this->date = $date;
    }

    /**
     * Returns the text.
     *
     * @return string $text
     */
    public function getText(): string
    {
        return $this->text;
    }

    /**
     * Returns the plain text without tags.
     *
     * @return string $text
     */
    public function getPlainText(): string
    {
        return strip_tags($this->text);
    }

    /**
     * Sets the text.
     *
     */
    public function setText(string $text)
    {
        $this->text = $text;
    }

    /**
     * Returns the approved.
     *
     * @return bool $approved
     */
    public function getApproved(): bool
    {
        return $this->approved;
    }

    /**
     * Sets the approved.
     *
     */
    public function setApproved(bool $approved)
    {
        $this->approved = (boolean) $approved;
    }

    /**
     * Returns the boolean state of approved.
     *
     */
    public function isApproved(): bool
    {
        return (boolean) $this->getApproved();
    }

    /**
     * Returns the spam.
     *
     * @return bool $spam
     */
    public function getSpam(): bool
    {
        return $this->spam;
    }

    /**
     * Sets the spam.
     *
     */
    public function setSpam(bool $spam)
    {
        $this->spam = (boolean) $spam;
    }


    public function setSpamPoints(int $spamPoints)
    {
        $this->spamPoints = $spamPoints;
    }


    public function getSpamPoints(): int
    {
        return $this->spamPoints;
    }

    /**
     * Returns the boolean state of spam.
     *
     */
    public function isSpam(): bool
    {
        return (boolean) $this->getSpam();
    }

    /**
     * Mark comment as spam.
     */
    public function markAsSpam()
    {
        $this->spam = true;
    }

    /**
     * Sets the postId.
     *
     */
    public function setPostId(int $postId)
    {
        $this->postId = $postId;
    }

    /**
     * Returns the post id.
     *
     */
    public function getPostId(): int
    {
        return $this->postId;
    }

    /**
     * Returns the post.
     *
     */
    public function getPost(): \FelixNagel\T3extblog\Domain\Model\Post
    {
        if ($this->post === null) {
            $this->post = $this->getPostRepository()->findByLocalizedUid($this->postId);
        }

        return $this->post;
    }

    /**
     * Returns the subscribe.
     *
     * @return bool $spam
     */
    public function getSubscribe(): bool
    {
        return (boolean) $this->subscribe;
    }

    /**
     * Sets the subscribe.
     *
     */
    public function setSubscribe(bool $subscribe)
    {
        $this->subscribe = (boolean) $subscribe;
    }


    public function setMailsSent(bool $mailsSent)
    {
        $this->mailsSent = (boolean) $mailsSent;
    }


    public function getMailsSent(): bool
    {
        return (boolean) $this->mailsSent;
    }


    public function hasPrivacyPolicyAccepted(): bool
    {
        return $this->privacyPolicyAccepted;
    }


    public function setPrivacyPolicyAccepted(bool $privacyPolicyAccepted)
    {
        $this->privacyPolicyAccepted = $privacyPolicyAccepted;
    }

    /**
     * If the comment is shown in frontend.
     */
    public function isValid(): bool
    {
        return !$this->spam && $this->approved && !$this->hidden && !$this->deleted;
    }
}
