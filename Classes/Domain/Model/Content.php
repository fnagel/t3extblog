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
    protected ?\DateTime $tstamp = null;

    protected string $CType = '';

    protected string $header = '';

    protected string $headerPosition = '';

    protected string $bodytext = '';

    protected int $colPos = 0;

    protected string $image = '';

    protected int $imagewidth = 0;

    protected int $imageorient = 0;

    protected string $imagecaption = '';

    protected int $imagecols = 0;

    protected int $imageborder = 0;

    protected string $media = '';

    protected string $layout = '';

    protected int $cols = 0;

    protected string $subheader = '';

    protected string $headerLink = '';

    protected string $imageLink = '';

    protected bool $imageZoom = false;

    protected string $altText = '';

    protected string $titleText = '';

    protected string $headerLayout = '';

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

    public function getColPos(): int
    {
        return $this->colPos;
    }

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
