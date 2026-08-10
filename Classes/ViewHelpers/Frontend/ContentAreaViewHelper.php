<?php

namespace FelixNagel\T3extblog\ViewHelpers\Frontend;

/**
 * This file is part of the "t3extblog" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use FelixNagel\T3extblog\Domain\Model\Post;
use TYPO3\CMS\Core\Page\ContentArea;
use TYPO3\CMS\Core\Page\ContentSlideMode;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * View helper for creating a content area of the content elements of a post.
 */
class ContentAreaViewHelper extends AbstractViewHelper
{
    public function initializeArguments(): void
    {
        parent::initializeArguments();

        $this->registerArgument('post', Post::class, 'Post entity for which a content area should be created.', true);
    }

    public function render(): ContentArea
    {
        /* @var $post Post */
        $post = $this->arguments['post'];
        $result = [];

        foreach ($post->getContent() as $item) {
            $result[] = RecordViewHelper::getRecord($item);
        }

        return new ContentArea(
            't3extblog-post-content',
            't3extblog-post-content',
            1786287509,
            ContentSlideMode::None,
            [],
            [],
            [],
            $result
        );
    }
}
