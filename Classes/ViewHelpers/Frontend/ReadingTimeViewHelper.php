<?php

namespace FelixNagel\T3extblog\ViewHelpers\Frontend;

/**
 * This file is part of the "t3extblog" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use FelixNagel\T3extblog\Domain\Model\Content;
use FelixNagel\T3extblog\Domain\Model\Post;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * ViewHelper to calculate and render reading time.
 */
class ReadingTimeViewHelper extends AbstractViewHelper
{
    public function initializeArguments(): void
    {
        $this->registerArgument('post', Post::class, 'Post to calculate reading time for', true);
        $this->registerArgument('wordsPerMinute', 'int', 'Words per minute reading speed', false, 250);
    }

    public function render(): int
    {
        $wordsPerMinute = (int)$this->arguments['wordsPerMinute'];

        $value = '';
        $post = $this->arguments['post'];
        if ($post instanceof Post) {
            /* @var Content $contentElement */
            foreach ($post->getContent() as $contentElement) {
                $value .= $contentElement->getHeader().' '.$contentElement->getBodytext();
            }
        } else {
            $value = $this->renderChildren();
        }

        return ceil(str_word_count(strip_tags($value)) / $wordsPerMinute);
    }
}
