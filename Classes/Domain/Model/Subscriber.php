<?php

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
 *
 *
 * @package t3extblog
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 *
 */
class Tx_T3extblog_Domain_Model_Subscriber extends Tx_T3extblog_Domain_Model_AbstractEntity {

	/**
	 * @var boolean
	 */
	protected $hidden = TRUE;

	/**
	 * @var boolean
	 */
	protected $deleted;

	/**
	 * email
	 *
	 * @var string
	 * @validate NotEmpty
	 */
	protected $email;

	/**
	 * name
	 *
	 * @var string
	 * @validate NotEmpty
	 */
	protected $name;

	/**
	 * postUid
	 *
	 * @var integer
	 * @validate NotEmpty
	 */
	protected $postUid;

	/**
	 * post
	 *
	 * @var Tx_T3extblog_Domain_Model_Post
	 * @lazy
	 */
	protected $post = NULL;

	/**
	 * comments
	 *
	 * @var Tx_Extbase_Persistence_ObjectStorage<Tx_T3extblog_Domain_Model_Comment>
	 * @lazy
	 */
	protected $postComments = NULL;

	/**
	 * comments
	 *
	 * @var Tx_Extbase_Persistence_ObjectStorage<Tx_T3extblog_Domain_Model_Comment>
	 * @lazy
	 */
	protected $postPendingComments = NULL;

	/**
	 * lastSent
	 *
	 * @var DateTime
	 * @validate NotEmpty
	 */
	protected $lastSent = NULL;

	/**
	 * code
	 *
	 * @var string
	 * @validate NotEmpty
	 */
	protected $code;


	/**
	 * __construct
	 */
	public function __construct($postUid) {
		$this->postUid = $postUid;
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
	 * Returns the name
	 *
	 * @return string $name
	 */
	public function getName() {
		return $this->name;
	}

	/**
	 * Sets the name
	 *
	 * @param string $name
	 *
	 * @return void
	 */
	public function setName($name) {
		$this->name = $name;
	}

	/**
	 * Returns the postUid
	 *
	 * @return integer $postUid
	 */
	public function getPostUid() {
		return $this->postUid;
	}

	/**
	 * Returns the post
	 *
	 * @return Tx_T3extblog_Domain_Model_Post $post
	 */
	public function getPost() {
		if ($this->post === NULL) {
			$this->post = $this->getPostRepository()->findByUid($this->postUid);
		}

		return $this->post;
	}

	/**
	 * Returns the post comments
	 *
	 * @return Tx_Extbase_Persistence_ObjectStorage<Tx_T3extblog_Domain_Model_Comment> $comments
	 */
	public function getPostComments() {
		if ($this->postComments === NULL) {
			$postComments = $this->getCommentRepository()->findValidByEmailAndPostId($this->email, $this->postUid);

			$this->postComments = new Tx_Extbase_Persistence_ObjectStorage();
			foreach ($postComments as $comment) {
				$this->postComments->attach($comment);
			}
		}

		return $this->postComments;
	}

	/**
	 * Returns the post pending comments
	 *
	 * @return Tx_Extbase_Persistence_ObjectStorage<Tx_T3extblog_Domain_Model_Comment> $comments
	 */
	public function getPostPendingComments() {
		if ($this->postPendingComments === NULL) {
			$postPendingComments = $this->getCommentRepository()->findPendingByEmailAndPostId($this->email, $this->postUid);

			$this->postPendingComments = new Tx_Extbase_Persistence_ObjectStorage();
			foreach ($postPendingComments as $comment) {
				$this->postPendingComments->attach($comment);
			}
		}

		return $this->postPendingComments;
	}

	/**
	 * Sets the postUid
	 *
	 * @param integer $postUid
	 *
	 * @return void
	 */
	public function setPostUid($postUid) {
		$this->postUid = $postUid;
	}

	/**
	 * Returns the lastSent
	 *
	 * @return DateTime $lastSent
	 */
	public function getLastSent() {
		return $this->lastSent;
	}

	/**
	 * Sets the lastSent
	 *
	 * @param DateTime $lastSent
	 *
	 * @return void
	 */
	public function setLastSent($lastSent) {
		$this->lastSent = $lastSent;
	}

	/**
	 * Returns the code
	 *
	 * @return string $code
	 */
	public function getCode() {
		return $this->code;
	}

	/**
	 * Sets the code
	 *
	 * @param string $code
	 *
	 * @return void
	 */
	public function setCode($code) {
		$this->code = $code;
	}

	/**
	 * Creates a code
	 *
	 * @return void
	 */
	private function createCode() {
		$now = new DateTime();
		$input = $this->email . $this->postUid . $now->getTimestamp() . uniqid();

		$this->code = substr(t3lib_div::hmac($input), 0, 32);
	}

	/**
	 * Update subscriber
	 *
	 * @return void
	 */
	public function updateAuth() {
		$this->setLastSent(new DateTime());
		$this->createCode();
	}

	/**
	 * Returns prepared mailto array
	 *
	 * @return array
	 */
	public function getMailTo() {
		return array($this->getEmail() => $this->getName());
	}

	/**
	 * Checks if the authCode is still valid
	 *
	 * @param DateTime $expireDate
	 *
	 * @return string
	 */
	public function isAuthCodeExpired($expireDate) {
		$now = new DateTime();
		$expire = clone $this->getLastSent();

		if ($now > $expire->modify($expireDate)) {
			return TRUE;
		}

		return FALSE;
	}

}

?>