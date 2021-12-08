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
    /**
     * @var bool
     */
    protected bool $hidden = false;

    /**
     * @var bool
     */
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
     * @var bool
     */
    protected bool $approved = false;

    /**
     * spam.
     *
     * @var bool
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
     * @var bool
     */
    protected bool $subscribe = false;

    /**
     * If the notification mails are already sent.
     *
     * @var bool
     */
    protected bool $mailsSent = false;

    /**
     * privacy policy accepted.
     *
     * @var bool
     */
    protected bool $privacyPolicyAccepted = false;

    /**
     * __construct.
     */
    public function __construct()
    {
        $this->date = new \DateTime();
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
     * @return string $author
     */
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * Sets the author.
     *
     * @param string $author
     */
    public function setAuthor($author)
    {
        $this->author = $author;
    }

    /**
     * Returns the email.
     *
     * @return string $email
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Sets the email.
     *
     * @param string $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * Returns the website.
     *
     * @return string $website
     */
    public function getWebsite()
    {
        return $this->website;
    }

    /**
     * Sets the website.
     *
     * @param string $website
     */
    public function setWebsite($website)
    {
        $this->website = $website;
    }

    /**
     * Returns the date.
     *
     * @return \DateTime $date
     */
    public function getDate()
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
    public function getText()
    {
        return $this->text;
    }

    /**
     * Returns the plain text without tags.
     *
     * @return string $text
     */
    public function getPlainText()
    {
        return strip_tags($this->text);
    }

    /**
     * Sets the text.
     *
     * @param string $text
     */
    public function setText($text)
    {
        $this->text = $text;
    }

    /**
     * Returns the approved.
     *
     * @return bool $approved
     */
    public function getApproved()
    {
        return $this->approved;
    }

    /**
     * Sets the approved.
     *
     * @param bool $approved
     */
    public function setApproved($approved)
    {
        $this->approved = (boolean) $approved;
    }

    /**
     * Returns the boolean state of approved.
     *
     * @return bool
     */
    public function isApproved()
    {
        return (boolean) $this->getApproved();
    }

    /**
     * Returns the spam.
     *
     * @return bool $spam
     */
    public function getSpam()
    {
        return $this->spam;
    }

    /**
     * Sets the spam.
     *
     * @param bool $spam
     */
    public function setSpam($spam)
    {
        $this->spam = (boolean) $spam;
    }

    /**
     * @param int $spamPoints
     */
    public function setSpamPoints($spamPoints)
    {
        $this->spamPoints = $spamPoints;
    }

    /**
     * @return int
     */
    public function getSpamPoints()
    {
        return $this->spamPoints;
    }

    /**
     * Returns the boolean state of spam.
     *
     * @return bool
     */
    public function isSpam()
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
     * @param int $postId
     */
    public function setPostId($postId)
    {
        $this->postId = $postId;
    }

    /**
     * Returns the post id.
     *
     * @return int
     */
    public function getPostId()
    {
        return $this->postId;
    }

    /**
     * Returns the post.
     *
     * @return \FelixNagel\T3extblog\Domain\Model\Post
     */
    public function getPost()
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
    public function getSubscribe()
    {
        return (boolean) $this->subscribe;
    }

    /**
     * Sets the subscribe.
     *
     * @param bool $subscribe
     */
    public function setSubscribe($subscribe)
    {
        $this->subscribe = (boolean) $subscribe;
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
     * @return bool
     */
    public function hasPrivacyPolicyAccepted()
    {
        return $this->privacyPolicyAccepted;
    }

    /**
     * @param bool $privacyPolicyAccepted
     */
    public function setPrivacyPolicyAccepted($privacyPolicyAccepted)
    {
        $this->privacyPolicyAccepted = $privacyPolicyAccepted;
    }

    /**
     * If the comment is shown in frontend.
     *
     * @return bool
     */
    public function isValid()
    {
        return !$this->spam && $this->approved && !$this->hidden && !$this->deleted;
    }
}
