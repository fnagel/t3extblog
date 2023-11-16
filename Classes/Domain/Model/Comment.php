<?php

namespace FelixNagel\T3extblog\Domain\Model;

/**
 * This file is part of the "t3extblog" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use FelixNagel\T3extblog\Validation\Validator\UrlValidator;
use TYPO3\CMS\Extbase\Annotation\ORM\Lazy;
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

    #[Extbase\Validate(['validator' => 'Text'])]
    protected ?string $title = null;

    #[Extbase\Validate(['validator' => 'NotEmpty'])]
    #[Extbase\Validate(['validator' => 'Text'])]
    protected ?string $author = null;

    #[Extbase\Validate(['validator' => 'EmailAddress'])]
    protected ?string $email = null;

    #[Extbase\Validate(['validator' => UrlValidator::class])]
    protected ?string $website = null;

    protected ?\DateTime $date = null;

    #[Extbase\Validate(['validator' => 'NotEmpty'])]
    protected ?string $text = null;

    protected bool $approved = false;

    protected bool $spam = false;

    protected ?int $spamPoints = null;

    protected ?int $postId = null;

    #[Lazy]
    protected ?Post $post = null;

    /**
     * subscribe (not persisted).
     */
    protected bool $subscribe = false;

    /**
     * If the notification mails are already sent.
     */
    protected bool $mailsSent = false;

    protected bool $privacyPolicyAccepted = false;

    public function __construct()
    {
        $this->date = new \DateTime();
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

    public function getAuthor(): string
    {
        return $this->author;
    }

    public function setAuthor(string $author): void
    {
        $this->author = $author;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    public function getWebsite(): string
    {
        return $this->website;
    }

    public function setWebsite(string $website): void
    {
        $this->website = $website;
    }

    public function getDate(): ?\DateTime
    {
        return $this->date;
    }

    public function setDate(\DateTime $date): void
    {
        $this->date = $date;
    }

    public function getText(): string
    {
        return $this->text;
    }

    /**
     * Returns the plain text without tags.
     */
    public function getPlainText(): string
    {
        return strip_tags($this->text);
    }

    public function setText(string $text): void
    {
        $this->text = $text;
    }

    public function getApproved(): bool
    {
        return $this->approved;
    }

    public function setApproved(bool $approved): void
    {
        $this->approved = (boolean) $approved;
    }

    public function isApproved(): bool
    {
        return $this->getApproved();
    }

    public function getSpam(): bool
    {
        return $this->spam;
    }

    public function setSpam(bool $spam): void
    {
        $this->spam = $spam;
    }

    public function setSpamPoints(int $spamPoints): void
    {
        $this->spamPoints = $spamPoints;
    }

    public function getSpamPoints(): int
    {
        return $this->spamPoints;
    }

    public function isSpam(): bool
    {
        return $this->getSpam();
    }

    /**
     * Mark comment as spam.
     */
    public function markAsSpam(): void
    {
        $this->spam = true;
    }

    public function setPostId(int $postId): void
    {
        $this->postId = $postId;
    }

    public function getPostId(): int
    {
        return $this->postId;
    }

    public function getPost(): ?Post
    {
        if ($this->post === null && $this->postId !== null) {
            $this->post = $this->getPostRepository()->findByLocalizedUid($this->postId);
        }

        return $this->post;
    }

    public function getSubscribe(): bool
    {
        return $this->subscribe;
    }

    public function setSubscribe(bool $subscribe): void
    {
        $this->subscribe = $subscribe;
    }

    public function setMailsSent(bool $mailsSent): void
    {
        $this->mailsSent = $mailsSent;
    }

    public function getMailsSent(): bool
    {
        return $this->mailsSent;
    }

    public function hasPrivacyPolicyAccepted(): bool
    {
        return $this->privacyPolicyAccepted;
    }

    public function setPrivacyPolicyAccepted(bool $privacyPolicyAccepted): void
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
