<?php

namespace FelixNagel\T3extblog\ViewHelpers\Frontend;

/**
 * This file is part of the "t3extblog" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use FelixNagel\T3extblog\Utility\FrontendUtility;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractConditionViewHelper;
use FelixNagel\T3extblog\Domain\Model\Post;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;

/**
 * CommentAllowedViewHelper.
 */
class CommentAllowedViewHelper extends AbstractConditionViewHelper
{
    public function initializeArguments(): void
    {
        parent::initializeArguments();

        $this->registerArgument(
            'post',
            Post::class,
            'Post object to check if new comments are allowed.',
            true
        );
    }

    public static function verdict(array $arguments, RenderingContextInterface $renderingContext): bool
    {
        /* @var Post $post */
        $post = $arguments['post'];
        $settings = $renderingContext->getVariableProvider()->get('settings');

        if (!$settings['blogsystem']['comments']['allowed'] || $post->getAllowComments() === Post::ALLOW_COMMENTS_NOBODY) {
            return false;
        }

        if ($post->getAllowComments() === Post::ALLOW_COMMENTS_LOGIN && !FrontendUtility::isUserLoggedIn()
        ) {
            return false;
        }

        return !($settings['blogsystem']['comments']['allowedUntil']
            && $post->isExpired(trim($settings['blogsystem']['comments']['allowedUntil'])));
    }
}
