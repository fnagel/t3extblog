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
class Tx_T3extblog_Domain_Model_Content extends Tx_Extbase_DomainObject_AbstractEntity {

	/**
	 * @var DateTime
	 */
	protected $crdate;

	/**
	 * @var DateTime
	 */
	protected $tstamp;

	/**
	 * @var string
	 */
	protected $CType;

	/**
	 * @var string
	 */
	protected $header;

	/**
	 * @var string
	 */
	protected $headerPosition;

	/**
	 * @var string
	 */
	protected $bodytext;

	/**
	 * @var integer
	 */
	protected $colPos;

	/**
	 * @var string
	 */
	protected $image;

	/**
	 * @var integer
	 */
	protected $imagewidth;

	/**
	 * @var integer
	 */
	protected $imageorient;

	/**
	 * @var string
	 */
	protected $imagecaption;

	/**
	 * @var integer
	 */
	protected $imagecols;

	/**
	 * @var integer
	 */
	protected $imageborder;

	/**
	 * @var string
	 */
	protected $media;

	/**
	 * @var string
	 */
	protected $layout;

	/**
	 * @var integer
	 */
	protected $cols;

	/**
	 * @var string
	 */
	protected $subheader;

	/**
	 * @var string
	 */
	protected $headerLink;

	/**
	 * @var string
	 */
	protected $imageLink;

	/**
	 * @var string
	 */
	protected $imageZoom;

	/**
	 * @var string
	 */
	protected $altText;

	/**
	 * @var string
	 */
	protected $titleText;

	/**
	 * @var string
	 */
	protected $headerLayout;

	/**
	 * @var string
	 */
	protected $listType;


	/**
	 * @return DateTime
	 */
	public function getCrdate() {
		return $this->crdate;
	}

	/**
	 * @return DateTime
	 */
	public function getTstamp() {
		return $this->tstamp;
	}

	/**
	 * @return string
	 */
	public function getCType() {
		return $this->CType;
	}

	/**
	 * @return string
	 */
	public function getHeader() {
		return $this->header;
	}

	/**
	 * @return string
	 */
	public function getHeaderPosition() {
		return $this->headerPosition;
	}

	/**
	 * @return string
	 */
	public function getBodytext() {
		return $this->bodytext;
	}

	/**
	 * Get the colpos
	 *
	 * @return integer
	 */
	public function getColPos() {
		return (int)$this->colPos;
	}

	/**
	 * @return string
	 */
	public function getImage() {
		return $this->image;
	}

	/**
	 * @return int
	 */
	public function getImagewidth() {
		return $this->imagewidth;
	}

	/**
	 * @return int
	 */
	public function getImageorient() {
		return $this->imageorient;
	}

	/**
	 * @return string
	 */
	public function getImagecaption() {
		return $this->imagecaption;
	}

	/**
	 * @return int
	 */
	public function getImagecols() {
		return $this->imagecols;
	}

	/**
	 * @return int
	 */
	public function getImageborder() {
		return $this->imageborder;
	}

	/**
	 * @return string
	 */
	public function getMedia() {
		return $this->media;
	}

	/**
	 * @return string
	 */
	public function getLayout() {
		return $this->layout;
	}

	/**
	 * @return int
	 */
	public function getCols() {
		return $this->cols;
	}

	/**
	 * @return string
	 */
	public function getSubheader() {
		return $this->subheader;
	}

	/**
	 * @return string
	 */
	public function getHeaderLink() {
		return $this->headerLink;
	}

	/**
	 * @return string
	 */
	public function getImageLink() {
		return $this->imageLink;
	}

	/**
	 * @return string
	 */
	public function getImageZoom() {
		return $this->imageZoom;
	}

	/**
	 * @return string
	 */
	public function getAltText() {
		return $this->altText;
	}

	/**
	 * @return string
	 */
	public function getTitleText() {
		return $this->titleText;
	}

	/**
	 * @return string
	 */
	public function getHeaderLayout() {
		return $this->headerLayout;
	}

	/**
	 * @return string
	 */
	public function getListType() {
		return $this->listType;
	}

}

?>