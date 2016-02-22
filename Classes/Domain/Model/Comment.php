<?php

namespace TYPO3\T3extblog\Domain\Model;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2013-2015 Felix Nagel <info@felixnagel.com>
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

/**
 * Comment
 */
class Comment extends AbstractEntity {

	/**
	 * @var boolean
	 */
	protected $hidden = FALSE;

	/**
	 * @var boolean
	 */
	protected $deleted;

	/**
	 * title
	 *
	 * @validate Text
	 * @var string
	 */
	protected $title;

	/**
	 * author
	 *
	 * @validate Text
	 * @validate NotEmpty
	 * @var string
	 */
	protected $author;

	/**
	 * email
	 *
	 * @var string
	 * @validate NotEmpty
	 * @validate EmailAddress
	 */
	protected $email;

	/**
	 * website
	 *
	 * @var string
	 * @validate \TYPO3\T3extblog\Validation\Validator\UrlValidator
	 */
	protected $website;

	/**
	 * date
	 *
	 * @var \DateTime
	 */
	protected $date;

	/**
	 * text
	 *
	 * @var string
	 * @validate NotEmpty
	 */
	protected $text;

	/**
	 * approved
	 *
	 * @var boolean
	 */
	protected $approved = FALSE;

	/**
	 * spam
	 *
	 * @var boolean
	 */
	protected $spam = FALSE;

	/**
	 * spamPoints
	 *
	 * @var integer
	 */
	protected $spamPoints = NULL;

	/**
	 * postId
	 *
	 * @var integer
	 */
	protected $postId;

	/**
	 * post
	 *
	 * @var \TYPO3\T3extblog\Domain\Model\Post
	 * @lazy
	 */
	protected $post = NULL;

	/**
	 * subscribe (not persisted)
	 *
	 * @var boolean
	 */
	protected $subscribe = FALSE;

	/**
	 * If the notification mails are already sent
	 *
	 * @var boolean
	 */
	protected $mailsSent = FALSE;


	/**
	 * __construct
	 */
	public function __construct() {
		$this->date = new \DateTime();
	}

	/**
	 * @param boolean $deleted
	 *
	 * @return void
	 */
	public function setDeleted($deleted) {
		$this->deleted = $deleted;
	}

	/**
	 * @return boolean
	 */
	public function getDeleted() {
		return $this->deleted;
	}

	/**
	 * @param boolean $hidden
	 *
	 * @return void
	 */
	public function setHidden($hidden) {
		$this->hidden = $hidden;
	}

	/**
	 * @return boolean
	 */
	public function getHidden() {
		return $this->hidden;
	}

	/**
	 * Returns the title
	 *
	 * @return string $title
	 */
	public function getTitle() {
		return $this->title;
	}

	/**
	 * Sets the title
	 *
	 * @param string $title
	 *
	 * @return void
	 */
	public function setTitle($title) {
		$this->title = $title;
	}

	/**
	 * Returns the author
	 *
	 * @return string $author
	 */
	public function getAuthor() {
		return $this->author;
	}

	/**
	 * Sets the author
	 *
	 * @param string $author
	 *
	 * @return void
	 */
	public function setAuthor($author) {
		$this->author = $author;
	}

	/**
	 * Returns the email
	 *
	 * @return string $email
	 */
	public function getEmail() {
		return $this->email;
	}

	/**
	 * Sets the email
	 *
	 * @param string $email
	 *
	 * @return void
	 */
	public function setEmail($email) {
		$this->email = $email;
	}

	/**
	 * Returns the website
	 *
	 * @return string $website
	 */
	public function getWebsite() {
		return $this->website;
	}

	/**
	 * Sets the website
	 *
	 * @param string $website
	 *
	 * @return void
	 */
	public function setWebsite($website) {
		$this->website = $website;
	}

	/**
	 * Returns the date
	 *
	 * @return \DateTime $date
	 */
	public function getDate() {
		return $this->date;
	}

	/**
	 * Sets the date
	 *
	 * @param \DateTime $date
	 *
	 * @return void
	 */
	public function setDate($date) {
		$this->date = $date;
	}

	/**
	 * Returns the text
	 *
	 * @return string $text
	 */
	public function getText() {
		return $this->text;
	}

	/**
	 * Returns the plain text without tags
	 *
	 * @return string $text
	 */
	public function getPlainText() {
		return strip_tags($this->text);
	}

	/**
	 * Sets the text
	 *
	 * @param string $text
	 *
	 * @return void
	 */
	public function setText($text) {
		$this->text = $text;
	}

	/**
	 * Returns the approved
	 *
	 * @return boolean $approved
	 */
	public function getApproved() {
		return $this->approved;
	}

	/**
	 * Sets the approved
	 *
	 * @param boolean $approved
	 *
	 * @return void
	 */
	public function setApproved($approved) {
		$this->approved = (boolean) $approved;
	}

	/**
	 * Returns the boolean state of approved
	 *
	 * @return boolean
	 */
	public function isApproved() {
		return (boolean) $this->getApproved();
	}

	/**
	 * Returns the spam
	 *
	 * @return boolean $spam
	 */
	public function getSpam() {
		return $this->spam;
	}

	/**
	 * Sets the spam
	 *
	 * @param boolean $spam
	 *
	 * @return void
	 */
	public function setSpam($spam) {
		$this->spam = (boolean)$spam;
	}

	/**
	 * @param int $spamPoints
	 *
	 * @return void
	 */
	public function setSpamPoints($spamPoints) {
		$this->spamPoints = $spamPoints;
	}

	/**
	 * @return int
	 */
	public function getSpamPoints() {
		return $this->spamPoints;
	}

	/**
	 * Returns the boolean state of spam
	 *
	 * @return boolean
	 */
	public function isSpam() {
		return (boolean)$this->getSpam();
	}

	/**
	 * Mark comment as spam
	 *
	 * @return void
	 */
	public function markAsSpam() {
		$this->spam = TRUE;
	}

	/**
	 * Sets the postId
	 *
	 * @param integer $postId
	 *
	 * @return void
	 */
	public function setPostId($postId) {
		$this->postId = $postId;
	}

	/**
	 * Returns the post id
	 *
	 * @return integer
	 */
	public function getPostId() {
		return $this->postId;
	}

	/**
	 * Returns the post
	 *
	 * @return \TYPO3\T3extblog\Domain\Model\Post
	 */
	public function getPost() {
		if ($this->post === NULL) {
			$this->post = $this->getPostRepository()->findByLocalizedUid($this->postId);
		}

		return $this->post;
	}

	/**
	 * Returns the subscribe
	 *
	 * @return boolean $spam
	 */
	public function getSubscribe() {
		return (boolean) $this->subscribe;
	}

	/**
	 * Sets the subscribe
	 *
	 * @param boolean $subscribe
	 *
	 * @return void
	 */
	public function setSubscribe($subscribe) {
		$this->subscribe = (boolean) $subscribe;
	}

	/**
	 * @param boolean $mailsSent
	 *
	 * @return void
	 */
	public function setMailsSent($mailsSent) {
		$this->mailsSent = (boolean) $mailsSent;
	}

	/**
	 * @return boolean
	 */
	public function getMailsSent() {
		return (boolean) $this->mailsSent;
	}

	/**
	 * If the comment is shown in frontend
	 *
	 * @return boolean
	 */
	public function isValid() {
		return (!$this->spam && $this->approved && !$this->hidden && !$this->deleted);
	}
}
