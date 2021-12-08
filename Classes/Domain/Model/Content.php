<?php

namespace FelixNagel\T3extblog\Domain\Model;

/**
 * This file is part of the "t3extblog" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

/**
 * Content (tt_content).
 *
 * @SuppressWarnings("PHPMD.ExcessivePublicCount")
 * @SuppressWarnings("PHPMD.TooManyFields")
 */
class Content extends AbstractLocalizedEntity
{
    /**
     * @var \DateTime
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
     * @var int
     */
    protected $colPos;

    /**
     * @var string
     */
    protected $image;

    /**
     * @var int
     */
    protected $imagewidth;

    /**
     * @var int
     */
    protected $imageorient;

    /**
     * @var string
     */
    protected $imagecaption;

    /**
     * @var int
     */
    protected $imagecols;

    /**
     * @var int
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
     * @var int
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

    
    public function getTstamp(): \DateTime
    {
        return $this->tstamp;
    }

    /**
     * @param $tstamp
     */
    public function setTstamp($tstamp)
    {
        $this->tstamp = $tstamp;
    }

    
    public function getCType(): string
    {
        return $this->CType;
    }

    /**
     * @param $ctype
     */
    public function setCType($ctype)
    {
        $this->CType = $ctype;
    }

    
    public function getHeader(): string
    {
        return $this->header;
    }

    /**
     * @param $header
     */
    public function setHeader($header)
    {
        $this->header = $header;
    }

    
    public function getHeaderPosition(): string
    {
        return $this->headerPosition;
    }

    /**
     * @param $headerPosition
     */
    public function setHeaderPosition($headerPosition)
    {
        $this->headerPosition = $headerPosition;
    }

    
    public function getBodytext(): string
    {
        return $this->bodytext;
    }

    /**
     * @param $bodytext
     */
    public function setBodytext($bodytext)
    {
        $this->bodytext = $bodytext;
    }

    /**
     * Get the colpos.
     *
     */
    public function getColPos(): int
    {
        return (int) $this->colPos;
    }

    /**
     * Set colpos.
     *
     */
    public function setColPos(int $colPos)
    {
        $this->colPos = $colPos;
    }

    
    public function getImage(): string
    {
        return $this->image;
    }

    /**
     * @param $image
     */
    public function setImage($image)
    {
        $this->image = $image;
    }

    
    public function getImagewidth(): int
    {
        return $this->imagewidth;
    }

    /**
     * @param $imagewidth
     */
    public function setImagewidth($imagewidth)
    {
        $this->imagewidth = $imagewidth;
    }

    
    public function getImageorient(): int
    {
        return $this->imageorient;
    }

    /**
     * @param $imageorient
     */
    public function setImageorient($imageorient)
    {
        $this->imageorient = $imageorient;
    }

    
    public function getImagecaption(): string
    {
        return $this->imagecaption;
    }

    /**
     * @param $imagecaption
     */
    public function setImagecaption($imagecaption)
    {
        $this->imagecaption = $imagecaption;
    }

    
    public function getImagecols(): int
    {
        return $this->imagecols;
    }

    /**
     * @param $imagecols
     */
    public function setImagecols($imagecols)
    {
        $this->imagecols = $imagecols;
    }

    
    public function getImageborder(): int
    {
        return $this->imageborder;
    }

    /**
     * @param $imageborder
     */
    public function setImageborder($imageborder)
    {
        $this->imageborder = $imageborder;
    }

    
    public function getMedia(): string
    {
        return $this->media;
    }

    /**
     * @param $media
     */
    public function setMedia($media)
    {
        $this->media = $media;
    }

    
    public function getLayout(): string
    {
        return $this->layout;
    }

    /**
     * @param $layout
     */
    public function setLayout($layout)
    {
        $this->layout = $layout;
    }

    
    public function getCols(): int
    {
        return $this->cols;
    }

    /**
     * @param $cols
     */
    public function setCols($cols)
    {
        $this->cols = $cols;
    }

    
    public function getSubheader(): string
    {
        return $this->subheader;
    }

    /**
     * @param $subheader
     */
    public function setSubheader($subheader)
    {
        $this->subheader = $subheader;
    }

    
    public function getHeaderLink(): string
    {
        return $this->headerLink;
    }

    /**
     * @param $headerLink
     */
    public function setHeaderLink($headerLink)
    {
        $this->headerLink = $headerLink;
    }

    
    public function getImageLink(): string
    {
        return $this->imageLink;
    }

    /**
     * @param $imageLink
     */
    public function setImageLink($imageLink)
    {
        $this->imageLink = $imageLink;
    }

    
    public function getImageZoom(): string
    {
        return $this->imageZoom;
    }

    /**
     * @param $imageZoom
     */
    public function setImageZoom($imageZoom)
    {
        $this->imageZoom = $imageZoom;
    }

    
    public function getAltText(): string
    {
        return $this->altText;
    }

    /**
     * @param $altText
     */
    public function setAltText($altText)
    {
        $this->altText = $altText;
    }

    
    public function getTitleText(): string
    {
        return $this->titleText;
    }

    /**
     * @param $titleText
     */
    public function setTitleText($titleText)
    {
        $this->titleText = $titleText;
    }

    
    public function getHeaderLayout(): string
    {
        return $this->headerLayout;
    }

    /**
     * @param $headerLayout
     */
    public function setHeaderLayout($headerLayout)
    {
        $this->headerLayout = $headerLayout;
    }

    
    public function getListType(): string
    {
        return $this->listType;
    }

    /**
     * @param $listType
     */
    public function setListType($listType)
    {
        $this->listType = $listType;
    }
}
