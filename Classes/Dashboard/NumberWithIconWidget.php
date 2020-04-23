<?php

namespace FelixNagel\T3extblog\Dashboard;

/**
 * This file is part of the "t3extblog" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use TYPO3\CMS\Dashboard\Widgets\ButtonProviderInterface;
use TYPO3\CMS\Dashboard\Widgets\NumberWithIconDataProviderInterface;
use TYPO3\CMS\Dashboard\Widgets\NumberWithIconWidget as CoreNumberWithIconWidget;
use TYPO3\CMS\Dashboard\Widgets\WidgetConfigurationInterface;
use TYPO3\CMS\Fluid\View\StandaloneView;

/**
 * AbstractNumberWithIconWidget
 *
 * Extends original widget with a button provider.
 */
class NumberWithIconWidget extends CoreNumberWithIconWidget
{
    public function __construct(
        WidgetConfigurationInterface $configuration,
        NumberWithIconDataProviderInterface $dataProvider,
        StandaloneView $view,
        ButtonProviderInterface $buttonProvider = null,
        array $options = []
    ) {
        parent::__construct($configuration, $dataProvider, $view, $options);

        $view->assignMultiple([
            'button' => $buttonProvider,
        ]);
    }
}
