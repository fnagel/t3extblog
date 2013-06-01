<?php

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2013 Felix Nagel <info@felixnagel.com>
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
class Tx_T3extblog_Domain_Model_Post extends Tx_Extbase_DomainObject_AbstractEntity {

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
	 * @var string
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
	 * subscriptions
	 *
	 * @var Tx_Extbase_Persistence_ObjectStorage<Tx_T3extblog_Domain_Model_Subscriber>
	 * @lazy
	 */
	protected $subscriptions;
	
	/**
	 * __construct
	 *
	 * @return void
	 */
	public function __construct() {
		$this->initStorageObjects();
	}

	/**
	 * Initializes all Tx_Extbase_Persistence_ObjectStorage properties.
	 *
	 * @return void
	 */
	protected function initStorageObjects() {
		$this->content = new Tx_Extbase_Persistence_ObjectStorage();		
		$this->categories = new Tx_Extbase_Persistence_ObjectStorage();
		$this->subscriptions = new Tx_Extbase_Persistence_ObjectStorage();
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
		if (intval($this->author)) {
			return t3lib_div::makeInstance("Tx_T3extblog_Domain_Repository_BackendUserRepository")->findByUid($this->author);
		}
		
		return NULL;
	}

	/**
	 * Sets the author
	 *
	 * @param Tx_T3extblog_Domain_Model_BackendUser|integer $author
	 * @return void
	 */
	public function setAuthor($author) {
		if ($author instanceof Tx_T3extblog_Domain_Model_BackendUser) {
			$this->author = $author->getUid();
		}	
		elseif (intval($author)) {
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
	 * @return DateTime $publishDate
	 */
	public function getPublishYear() {
		return $this->publishDate->format('Y');
	}
	
	/**
	 * Returns the publish month
	 *
	 * @return DateTime $publishDate
	 */
	public function getPublishMonth() {
		return $this->publishDate->format('m');
	}

	/**
	 * Sets the publishDate
	 *
	 * @param DateTime $publishDate
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
	 * Adds a Category
	 *
	 * @param Tx_T3extblog_Domain_Model_Category $categories
	 * @return void
	 */
	public function addCategory(Tx_T3extblog_Domain_Model_Category $category) {
		$this->categories->attach($category);
	}

	/**
	 * Removes a Category
	 *
	 * @param Tx_T3extblog_Domain_Model_Category $categoryToRemove The Category to be removed
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
	 * mapping does not work as relation is not bidirectional, using a repository instead
	 * and: its currently not possible to iterate via pagiante widget through storage objects
	 *
	 * @return void
	 */
	private function initComments() {	
		if ($this->comments == NULL) {
			$this->commentRepository = t3lib_div::makeInstance("Tx_T3extblog_Domain_Repository_CommentRepository");			
			$this->comments = $this->commentRepository->findForPost($this);
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
		$this->commentRepository->add($comment);
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
		$this->commentRepository->update($commentToRemove);
	}

	/**
	 * Returns the comments
	 *
	 * @return $comments
	 */
	public function getComments() {
		$this->initComments();
		
		return $this->comments;
	}


	/**
	 * Adds a Subscriber
	 *
	 * @param Tx_T3extblog_Domain_Model_Subscriber $subscription
	 * @return void
	 */
	public function addSubscription(Tx_T3extblog_Domain_Model_Subscriber $subscription) {
		$this->subscriptions->attach($subscription);
	}

	/**
	 * Removes a Subscriber
	 *
	 * @param Tx_T3extblog_Domain_Model_Subscriber $subscriptionToRemove The Subscriber to be removed
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
	 * @param Tx_Extbase_Persistence_ObjectStorage<Tx_T3extblog_Domain_Model_Subscriber> $subscriptions
	 * @return void
	 */
	public function setSubscriptions(Tx_Extbase_Persistence_ObjectStorage $subscriptions) {
		$this->subscriptions = $subscriptions;
	}
}
?>