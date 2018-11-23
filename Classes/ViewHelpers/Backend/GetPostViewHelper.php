<?php

namespace FelixNagel\T3extblog\ViewHelpers\Backend;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2015-2018 Felix Nagel <info@felixnagel.com>
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
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

use FelixNagel\T3extblog\Domain\Repository\PostRepository;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Fluid\ViewHelpers\Be\AbstractBackendViewHelper;

/**
 * Get a specific blog post record view helper.
 */
class GetPostViewHelper extends AbstractBackendViewHelper
{
    /**
     * postRepository.
     *
     * @var PostRepository
     */
    protected $postRepository = null;

    /**
     * @inheritdoc
     */
    public function initializeArguments()
    {
        parent::initializeArguments();

        $this->registerArgument('uid', 'int', 'UID of the post', false, null);
        $this->registerArgument('respectEnableFields', 'bool', 'If set to false, hidden records are shown', false, true);
    }

    /**
     * @return string
     */
    public function render()
    {
        $uid = $this->arguments['uid'];
        $respectEnableFields = $this->arguments['respectEnableFields'];

        if ($uid === null) {
            $uid = $this->renderChildren();
        }

        return $this->getPostRepository()->findByLocalizedUid($uid, $respectEnableFields);
    }

    /**
     * @return PostRepository
     */
    protected function getPostRepository()
    {
        if ($this->postRepository === null) {
            $this->postRepository = GeneralUtility::makeInstance(PostRepository::class);
        }

        return $this->postRepository;
    }
}
