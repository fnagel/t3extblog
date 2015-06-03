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
class Tx_T3extblog_Domain_Model_Post extends Tx_T3extblog_Domain_Model_AbstractLocalizedEntity {

	/**
	 * @var boolean
	 */
	protected $hidden = TRUE;

	/**
	 * @var boolean
	 */
	protected $deleted = FALSE;

	/**
	 * title
	 *
	 * @var string
	 * @validate NotEmpty
	 */
	protected $title;

	/**
	 * author
	 *
	 * @var Tx_T3extblog_Domain_Model_BackendUser
	 * @lazy
	 * @validate NotEmpty
	 */
	protected $author;

	/**
	 * publishDate
	 *
	 * @var DateTime
	 * @validate NotEmpty
	 */
	protected $publishDate;

	/**
	 * allowComments
	 *
	 * @var integer
	 */
	protected $allowComments;

	/**
	 * tagCloud
	 *
	 * @var string
	 */
	protected $tagCloud;

	/**
	 * numberOfViews
	 *
	 * @var integer
	 */
	protected $numberOfViews;

	/**
	 * content
	 *
	 * @var Tx_Extbase_Persistence_ObjectStorage<Tx_T3extblog_Domain_Model_Content>
	 * @lazy
	 */
	protected $content;

	/**
	 * categories
	 *
	 * @var Tx_Extbase_Persistence_ObjectStorage<Tx_T3extblog_Domain_Model_Category>
	 * @lazy
	 */
	protected $categories;

	/**
	 * comments
	 *
	 * @var Tx_Extbase_Persistence_ObjectStorage<Tx_T3extblog_Domain_Model_Comment>
	 * @lazy
	 */
	protected $comments = NULL;

	/**
	 * raw comments
	 *
	 * @var Tx_Extbase_Persistence_QueryResultInterface
	 * @lazy
	 */
	protected $rawComments = NULL;

	/**
	 * subscriptions
	 *
	 * @var Tx_Extbase_Persistence_ObjectStorage<Tx_T3extblog_Domain_Model_Subscriber>
	 * @lazy
	 */
	protected $subscriptions;

	/**
	 * __construct
	 */
	public function __construct() {
		$this->initStorageObjects();
	}

	/**
	 * Serialization (sleep) helper.
	 *
	 * @return array Names of the properties to be serialized
	 */
	public function __sleep() {
		parent::__sleep();

		$properties = get_object_vars($this);

		// fix to make sure we are able to use forward in controller
		unset($properties['categories']);
		unset($properties['subscriptions']);

		return array_keys($properties);
	}

	/**
	 * Initializes all Tx_Extbase_Persistence_ObjectStorage properties.
	 *
	 * @return void
	 */
	protected function initStorageObjects() {
		$this->categories = new Tx_Extbase_Persistence_ObjectStorage();
		$this->subscriptions = new Tx_Extbase_Persistence_ObjectStorage();
		$this->content = new Tx_Extbase_Persistence_ObjectStorage();
	}

	/**
	 * @param boolean $deleted
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
	 * @return Tx_T3extblog_Domain_Model_BackendUser $author
	 */
	public function getAuthor() {
		return $this->author;
	}

	/**
	 * Sets the author
	 *
	 * @param Tx_T3extblog_Domain_Model_BackendUser|integer $author
	 *
	 * @return void
	 */
	public function setAuthor($author) {
		if ($author instanceof Tx_T3extblog_Domain_Model_BackendUser) {
			$this->author = $author->getUid();
		} elseif (intval($author)) {
			$this->author = $author;
		}
	}

	/**
	 * Returns the publishDate
	 *
	 * @return DateTime $publishDate
	 */
	public function getPublishDate() {
		return $this->publishDate;
	}

	/**
	 * Returns the publish year
	 *
	 * @return string
	 */
	public function getPublishYear() {
		return $this->publishDate->format('Y');
	}

	/**
	 * Returns the publish month
	 *
	 * @return string
	 */
	public function getPublishMonth() {
		return $this->publishDate->format('m');
	}

	/**
	 * Returns the publish day
	 *
	 * @return string
	 */
	public function getPublishDay() {
		return $this->publishDate->format('d');
	}

	/**
	 * Checks if the post is too old for posting new comments
	 *
	 * @param string $expireDate
	 *
	 * @return string
	 */
	public function isExpired($expireDate = '+1 month') {
		$now = new DateTime();
		$expire = clone $this->getPublishDate();

		if ($now > $expire->modify($expireDate)) {
			return TRUE;
		}

		return FALSE;
	}

	/**
	 * Sets the publishDate
	 *
	 * @param DateTime $publishDate
	 *
	 * @return void
	 */
	public function setPublishDate($publishDate) {
		$this->publishDate = $publishDate;
	}

	/**
	 * Returns the allowComments
	 *
	 * @return integer $allowComments
	 */
	public function getAllowComments() {
		return $this->allowComments;
	}

	/**
	 * Sets the allowComments
	 *
	 * @param integer $allowComments
	 *
	 * @return void
	 */
	public function setAllowComments($allowComments) {
		$this->allowComments = $allowComments;
	}

	/**
	 * Returns the boolean state of allowComments
	 *
	 * @return boolean
	 */
	public function isAllowComments() {
		return $this->getAllowComments();
	}

	/**
	 * Returns the tagCloud
	 *
	 * @return array $tagCloud
	 */
	public function getTagCloud() {
		return t3lib_div::trimExplode(",", $this->tagCloud, true);
	}

	/**
	 * Sets the tagCloud
	 *
	 * @param string $tagCloud
	 *
	 * @return void
	 */
	public function setTagCloud($tagCloud) {
		if (is_array($tagCloud)) {
			$this->tagCloud = implode(", ", $tagCloud);
		} else {
			$this->tagCloud = $tagCloud;
		}
	}

	/**
	 * Returns the numberOfViews
	 *
	 * @return integer $numberOfViews
	 */
	public function getNumberOfViews() {
		return $this->numberOfViews;
	}

	/**
	 * Sets the numberOfViews
	 *
	 * @param integer $numberOfViews
	 *
	 * @return void
	 */
	public function setNumberOfViews($numberOfViews) {
		$this->numberOfViews = $numberOfViews;
	}

	/**
	 * Rise the numberOfViews
	 *
	 * @return void
	 */
	public function riseNumberOfViews() {
		$this->numberOfViews = $$this->numberOfViews + 1;
	}

	/**
	 * Returns the content
	 *
	 * @return Tx_Extbase_Persistence_ObjectStorage<Tx_T3extblog_Domain_Model_Content> $content
	 */
	public function getContent() {
		return $this->content;
	}

	/**
	 * Set content element list
	 *
	 * @param Tx_Extbase_Persistence_ObjectStorage $content content elements
	 * @return void
	 */
	public function setContent($content) {
		$this->content = $content;
	}

	/**
	 * Adds a content element to the record
	 *
	 * @param Tx_T3extblog_Domain_Model_Content $content
	 * @return void
	 */
	public function addContent(Tx_T3extblog_Domain_Model_Content $content) {
		if ($this->getContent() === NULL) {
			$this->content = new \TYPO3\CMS\Extbase\Persistence\ObjectStorage();
		}
		$this->content->attach($content);
	}

	/**
	 * Get id list of content elements
	 *
	 * @return string
	 */
	public function getContentIdList() {
		$idList = array();

		foreach ($this->getContent() as $contentElement) {
			$idList[] = $contentElement->getUid();
		}

		return implode(',', $idList);
	}

	/**
	 * Get all content elements bodytext field values concated without HTML tags
	 *
	 * @return string
	 */
	public function getPreview() {
		$text = array();

		foreach ($this->getContent() as $contentElement) {
			if (strlen($contentElement->getBodytext()) > 0) {
				$text[] = $contentElement->getBodytext();
			}
		}

		return strip_tags(implode('', $text));
	}

	/**
	 * Adds a Category
	 *
	 * @param Tx_T3extblog_Domain_Model_Category $category
	 *
	 * @return void
	 */
	public function addCategory(Tx_T3extblog_Domain_Model_Category $category) {
		$this->categories->attach($category);
	}

	/**
	 * Removes a Category
	 *
	 * @param Tx_T3extblog_Domain_Model_Category $categoryToRemove The Category to be removed
	 *
	 * @return void
	 */
	public function removeCategory(Tx_T3extblog_Domain_Model_Category $categoryToRemove) {
		$this->categories->detach($categoryToRemove);
	}

	/**
	 * Returns the categories
	 *
	 * @return Tx_Extbase_Persistence_ObjectStorage<Tx_T3extblog_Domain_Model_Category> $categories
	 */
	public function getCategories() {
		return $this->categories;
	}

	/**
	 * Inits comments
	 *
	 * Mapping does not work as relation is not bidirectional, using a repository instead
	 * And: its currently not possible to iterate via paginate widget through storage objects
	 *
	 * @return void
	 */
	protected function initComments() {
		if ($this->comments === NULL) {
			$this->rawComments = $this->getCommentRepository()->findValidByPost($this);

			$this->comments = new Tx_Extbase_Persistence_ObjectStorage();
			foreach($this->rawComments as $comment) {
				$this->comments->attach($comment);
			}
		}
	}

	/**
	 * Adds a Comment
	 *
	 * @param Tx_T3extblog_Domain_Model_Comment $comment
	 * @return void
	 */
	public function addComment(Tx_T3extblog_Domain_Model_Comment $comment) {
		$this->initComments();

		$comment->setPostId($this->getUid());
		$comment->_setProperty('_languageUid', $this->getSysLanguageUid());
		$this->comments->attach($comment);
		$this->getCommentRepository()->add($comment);
	}

	/**
	 * Removes a Comment
	 *
	 * @param Tx_T3extblog_Domain_Model_Comment $commentToRemove The Comment to be removed
	 * @return void
	 */
	public function removeComment(Tx_T3extblog_Domain_Model_Comment $commentToRemove) {
		$this->initComments();

		$commentToRemove->setDeleted(TRUE);

		$this->comments->detach($commentToRemove);
		$this->getCommentRepository()->update($commentToRemove);
	}

	/**
	 * Returns the comments
	 *
	 * @return Tx_Extbase_Persistence_ObjectStorage<Tx_T3extblog_Domain_Model_Comment> $comments
	 */
	public function getComments() {
		$this->initComments();

		return $this->comments;
	}

	/**
	 * Returns the comments
	 *
	 * @return Tx_Extbase_Persistence_QueryResultInterface<Tx_T3extblog_Domain_Model_Comment> $comments
	 */
	public function getCommentsForPaginate() {
		$this->initComments();

		return $this->rawComments;
	}

	/**
	 * Sets the comments
	 *
	 * @param Tx_Extbase_Persistence_ObjectStorage<Tx_T3extblog_Domain_Model_Comment> $comments
	 * @return void
	 */
	public function setComments(Tx_Extbase_Persistence_ObjectStorage $comments) {
		$this->comments = $comments;
	}

	/**
	 * Adds a Subscriber
	 *
	 * @param Tx_T3extblog_Domain_Model_Subscriber $subscription
	 *
	 * @return void
	 */
	public function addSubscription(Tx_T3extblog_Domain_Model_Subscriber $subscription) {
		$this->subscriptions->attach($subscription);
	}

	/**
	 * Removes a Subscriber
	 *
	 * @param Tx_T3extblog_Domain_Model_Subscriber $subscriptionToRemove The Subscriber to be removed
	 *
	 * @return void
	 */
	public function removeSubscription(Tx_T3extblog_Domain_Model_Subscriber $subscriptionToRemove) {
		$this->subscriptions->detach($subscriptionToRemove);
	}

	/**
	 * Returns the subscriptions
	 *
	 * @return Tx_Extbase_Persistence_ObjectStorage<Tx_T3extblog_Domain_Model_Subscriber> $subscriptions
	 */
	public function getSubscriptions() {
		return $this->subscriptions;
	}

	/**
	 * Sets the subscriptions
	 *
	 * @param Tx_Extbase_Persistence_ObjectStorage <Tx_T3extblog_Domain_Model_Subscriber> $subscriptions
	 *
	 * @return void
	 */
	public function setSubscriptions(Tx_Extbase_Persistence_ObjectStorage $subscriptions) {
		$this->subscriptions = $subscriptions;
	}

	/**
	 * Returns the permalink configuration
	 *
	 * @return array
	 */
	public function getLinkParameter() {
		return array(
			'post' => $this->getUid(),
			'day' => $this->getPublishDay(),
			'month' => $this->getPublishMonth(),
			'year' => $this->getPublishYear()
		);
	}
}

?>