<?php

namespace FelixNagel\T3extblog\EventListener\VisualEditor;

/**
 * This file is part of the "t3extblog" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use FelixNagel\T3extblog\Controller\PostController;
use TYPO3\CMS\Core\Attribute\AsEventListener;
use TYPO3\CMS\VisualEditor\Events\ModifyNewContentElementWizardUrlParameterEvent;

#[AsEventListener('ModifyNewContentElementWizardUrlParameter')]
class ModifyNewContentElementWizardUrlParameter
{
    public function __invoke(ModifyNewContentElementWizardUrlParameterEvent $event): void
    {
        $arguments = $event->getUsedArguments();

        // Add current route blog arguments to the new CE wizard URL for later use
        if (array_key_exists('tx_t3extblog_blogsystem', $arguments) &&
            PostController::isPostShowPageArguments($arguments)
        ) {
            $event->setParameters(array_merge($event->getParameters(), $arguments));
        }
    }
}
