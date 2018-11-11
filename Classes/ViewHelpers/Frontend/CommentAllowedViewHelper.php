<?php

namespace FelixNagel\T3extblog\ViewHelpers\Frontend;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2013-2018 Felix Nagel <info@felixnagel.com>
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

use FelixNagel\T3extblog\Utility\GeneralUtility;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractConditionViewHelper;
use FelixNagel\T3extblog\Domain\Model\Post;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;

/**
 * ViewHelper.
 */
class CommentAllowedViewHelper extends AbstractConditionViewHelper
{
    /**
     * {@inheritdoc}
     */
    public function initializeArguments()
    {
        parent::initializeArguments();

        $this->registerArgument(
            'post',
            Post::class,
            'Post object to check if new comments are allowed.',
            true
        );
    }

    /**
     * Check if a new comment is allowed.
     *
     * @inheritdoc
     */
    public static function renderStatic(array $arguments, \Closure $renderChildrenClosure, RenderingContextInterface $renderingContext)
    {
        $arguments['settings'] = $renderingContext->getVariableProvider()->get('settings');

        return parent::renderStatic($arguments, $renderChildrenClosure, $renderingContext);
    }

    /**
     * @inheritdoc
     */
    protected static function evaluateCondition($arguments = null)
    {
        /* @var Post $post */
        $post = $arguments['post'];
        $settings = $arguments['settings'];

        if (!$settings['blogsystem']['comments']['allowed'] || $post->getAllowComments() === 1) {
            return false;
        }

        if ($post->getAllowComments() === 2 && empty(GeneralUtility::getTsFe()->loginUser)) {
            return false;
        }

        if ($settings['blogsystem']['comments']['allowedUntil']) {
            if ($post->isExpired(trim($settings['blogsystem']['comments']['allowedUntil']))) {
                return false;
            }
        }

        return true;
    }
}
