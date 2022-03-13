<?php

namespace FelixNagel\T3extblog\ViewHelpers\Backend;

/**
 * This file is part of the "t3extblog" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use FelixNagel\T3extblog\Domain\Repository\PostRepository;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Fluid\ViewHelpers\Be\AbstractBackendViewHelper;

/**
 * Get a specific blog post record view helper.
 */
class GetPostViewHelper extends AbstractBackendViewHelper
{
    protected ?PostRepository $postRepository = null;

    public function initializeArguments()
    {
        parent::initializeArguments();

        $this->registerArgument('uid', 'int', 'UID of the post', false, null);
        $this->registerArgument('respectEnableFields', 'bool', 'If set to false, hidden records are shown', false, true);
    }


    public function render(): string
    {
        $uid = $this->arguments['uid'];
        $respectEnableFields = $this->arguments['respectEnableFields'];

        if ($uid === null) {
            $uid = $this->renderChildren();
        }

        return $this->getPostRepository()->findByLocalizedUid($uid, $respectEnableFields);
    }

    protected function getPostRepository(): PostRepository
    {
        if ($this->postRepository === null) {
            $this->postRepository = GeneralUtility::makeInstance(PostRepository::class);
        }

        return $this->postRepository;
    }
}
