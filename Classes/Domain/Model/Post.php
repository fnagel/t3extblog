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
	 * category
	 *
	 * @var Tx_Extbase_Persistence_ObjectStorage<Tx_T3extblog_Domain_Model_Category>
	 * @lazy
	 */
	protected $category;

	/**
	 * comments
	 *
	 * @var Tx_Extbase_Persistence_ObjectStorage<Tx_T3extblog_Domain_Model_Comment>
	 * @lazy
	 */
	protected $comments = NULL;

	/**
	 * __construct
	 *
	 * @return void
	 */
	public function __construct() {
		//Do not remove the next line: It would break the functionality
		$this->initStorageObjects();
	}

	/**
	 * Initializes all Tx_Extbase_Persistence_ObjectStorage properties.
	 *
	 * @return void
	 */
	protected function initStorageObjects() {
		/**
		 * Do not modify this method!
		 * It will be rewritten on each save in the extension builder
		 * You may modify the constructor of this class instead
		 */
		$this->content = new Tx_Extbase_Persistence_ObjectStorage();
		
		$this->category = new Tx_Extbase_Persistence_ObjectStorage();
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
	 * @param mixed $author
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
	 * Adds a Content
	 *
	 * @param Tx_T3extblog_Domain_Model_Content $content
	 * @return void
	 */
	public function addContent(Tx_T3extblog_Domain_Model_Content $content) {
		$this->content->attach($content);
	}

	/**
	 * Removes a Content
	 *
	 * @param Tx_T3extblog_Domain_Model_Content $contentToRemove The Content to be removed
	 * @return void
	 */
	public function removeContent(Tx_T3extblog_Domain_Model_Content $contentToRemove) {
		$this->content->detach($contentToRemove);
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
	 * Sets the content
	 *
	 * @param Tx_Extbase_Persistence_ObjectStorage<Tx_T3extblog_Domain_Model_Content> $content
	 * @return void
	 */
	public function setContent(Tx_Extbase_Persistence_ObjectStorage $content) {
		$this->content = $content;
	}

	/**
	 * Adds a Category
	 *
	 * @param Tx_T3extblog_Domain_Model_Category $category
	 * @return void
	 */
	public function addCategory(Tx_T3extblog_Domain_Model_Category $category) {
		$this->category->attach($category);
	}

	/**
	 * Removes a Category
	 *
	 * @param Tx_T3extblog_Domain_Model_Category $categoryToRemove The Category to be removed
	 * @return void
	 */
	public function removeCategory(Tx_T3extblog_Domain_Model_Category $categoryToRemove) {
		$this->category->detach($categoryToRemove);
	}

	/**
	 * Returns the category
	 *
	 * @return Tx_Extbase_Persistence_ObjectStorage<Tx_T3extblog_Domain_Model_Category> $category
	 */
	public function getCategory() {
		return $this->category;
	}

	/**
	 * Sets the category
	 *
	 * @param Tx_Extbase_Persistence_ObjectStorage<Tx_T3extblog_Domain_Model_Category> $category
	 * @return void
	 */
	public function setCategory(Tx_Extbase_Persistence_ObjectStorage $category) {
		$this->category = $category;
	}
		
	/**
	 * Adds a Comment
	 *
	 * @param Tx_T3extblog_Domain_Model_Comment $comment
	 * @return void
	 */
	public function addComment(Tx_T3extblog_Domain_Model_Comment $comment) {
		$comment->setPostId($this->getUid());
		t3lib_div::makeInstance("Tx_T3extblog_Domain_Repository_CommentRepository")->add($comment);
	}

	/**
	 * Removes a Comment
	 *
	 * @param Tx_T3extblog_Domain_Model_Comment $commentToRemove The Comment to be removed
	 * @return void
	 */
	public function removeComment(Tx_T3extblog_Domain_Model_Comment $commentToRemove) {
		$commentToRemove->setDeleted(TRUE);
		t3lib_div::makeInstance("Tx_T3extblog_Domain_Repository_CommentRepository")->update($commentToRemove);
	}

	/**
	 * Returns the comments
	 *
	 * @return $comments
	 */
	public function getComments() {
		return t3lib_div::makeInstance("Tx_T3extblog_Domain_Repository_CommentRepository")->findByFkPost($this->getUid());
	}

}
?>