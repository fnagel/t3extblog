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
     * @var \DateTime|null
     */
    protected ?\DateTime $tstamp = null;

    /**
     * @var string
     */
    protected string $CType = '';

    /**
     * @var string
     */
    protected string $header = '';

    /**
     * @var string
     */
    protected string $headerPosition = '';

    /**
     * @var string
     */
    protected string $bodytext = '';

    /**
     * @var int
     */
    protected int $colPos = 0;

    /**
     * @var string
     */
    protected string $image = '';

    /**
     * @var int
     */
    protected int $imagewidth = 0;

    /**
     * @var int
     */
    protected int $imageorient = 0;

    /**
     * @var string
     */
    protected string $imagecaption = '';

    /**
     * @var int
     */
    protected int $imagecols = 0;

    /**
     * @var int
     */
    protected int $imageborder = 0;

    /**
     * @var string
     */
    protected string $media = '';

    /**
     * @var string
     */
    protected string $layout = '';

    /**
     * @var int
     */
    protected int $cols = 0;

    /**
     * @var string
     */
    protected string $subheader = '';

    /**
     * @var string
     */
    protected string $headerLink = '';

    /**
     * @var string
     */
    protected string $imageLink = '';

    /**
     * @var bool
     */
    protected bool $imageZoom = false;

    /**
     * @var string
     */
    protected string $altText = '';

    /**
     * @var string
     */
    protected string $titleText = '';

    /**
     * @var string
     */
    protected string $headerLayout = '';

    /**
     * @var string
     */
    protected string $listType = '';


    public function getTstamp(): ?\DateTime
    {
        return $this->tstamp;
    }


    public function setTstamp($tstamp): void
    {
        $this->tstamp = $tstamp;
    }


    public function getCType(): string
    {
        return $this->CType;
    }


    public function setCType(string $ctype): void
    {
        $this->CType = $ctype;
    }


    public function getHeader(): string
    {
        return $this->header;
    }


    public function setHeader(string $header): void
    {
        $this->header = $header;
    }


    public function getHeaderPosition(): string
    {
        return $this->headerPosition;
    }


    public function setHeaderPosition(string $headerPosition): void
    {
        $this->headerPosition = $headerPosition;
    }


    public function getBodytext(): string
    {
        return $this->bodytext;
    }


    public function setBodytext(string $bodytext): void
    {
        $this->bodytext = $bodytext;
    }

    /**
     * Get the colpos.
     *
     */
    public function getColPos(): int
    {
        return $this->colPos;
    }

    /**
     * Set colpos.
     *
     */
    public function setColPos(int $colPos): void
    {
        $this->colPos = $colPos;
    }


    public function getImage(): string
    {
        return $this->image;
    }


    public function setImage(string $image): void
    {
        $this->image = $image;
    }


    public function getImagewidth(): int
    {
        return $this->imagewidth;
    }


    public function setImagewidth(int $imagewidth): void
    {
        $this->imagewidth = $imagewidth;
    }


    public function getImageorient(): int
    {
        return $this->imageorient;
    }


    public function setImageorient(int $imageorient): void
    {
        $this->imageorient = $imageorient;
    }


    public function getImagecaption(): string
    {
        return $this->imagecaption;
    }


    public function setImagecaption(string $imagecaption): void
    {
        $this->imagecaption = $imagecaption;
    }


    public function getImagecols(): int
    {
        return $this->imagecols;
    }


    public function setImagecols(int $imagecols): void
    {
        $this->imagecols = $imagecols;
    }


    public function getImageborder(): int
    {
        return $this->imageborder;
    }


    public function setImageborder(int $imageborder): void
    {
        $this->imageborder = $imageborder;
    }


    public function getMedia(): string
    {
        return $this->media;
    }


    public function setMedia(string $media): void
    {
        $this->media = $media;
    }


    public function getLayout(): string
    {
        return $this->layout;
    }


    public function setLayout(string $layout): void
    {
        $this->layout = $layout;
    }


    public function getCols(): int
    {
        return $this->cols;
    }


    public function setCols(int $cols): void
    {
        $this->cols = $cols;
    }


    public function getSubheader(): string
    {
        return $this->subheader;
    }


    public function setSubheader(string $subheader): void
    {
        $this->subheader = $subheader;
    }


    public function getHeaderLink(): string
    {
        return $this->headerLink;
    }


    public function setHeaderLink(string $headerLink): void
    {
        $this->headerLink = $headerLink;
    }


    public function getImageLink(): string
    {
        return $this->imageLink;
    }


    public function setImageLink(string $imageLink): void
    {
        $this->imageLink = $imageLink;
    }


    public function getImageZoom(): bool
    {
        return $this->imageZoom;
    }


    public function setImageZoom(bool $imageZoom): void
    {
        $this->imageZoom = $imageZoom;
    }


    public function getAltText(): string
    {
        return $this->altText;
    }


    public function setAltText(string $altText): void
    {
        $this->altText = $altText;
    }


    public function getTitleText(): string
    {
        return $this->titleText;
    }


    public function setTitleText(string $titleText): void
    {
        $this->titleText = $titleText;
    }


    public function getHeaderLayout(): string
    {
        return $this->headerLayout;
    }


    public function setHeaderLayout(string $headerLayout): void
    {
        $this->headerLayout = $headerLayout;
    }


    public function getListType(): string
    {
        return $this->listType;
    }


    public function setListType(string $listType): void
    {
        $this->listType = $listType;
    }
}
