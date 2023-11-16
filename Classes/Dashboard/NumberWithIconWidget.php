<?php

namespace FelixNagel\T3extblog\Dashboard;

/**
 * This file is part of the "t3extblog" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Backend\View\BackendViewFactory;
use TYPO3\CMS\Dashboard\Widgets\ButtonProviderInterface;
use TYPO3\CMS\Dashboard\Widgets\NumberWithIconDataProviderInterface;
use TYPO3\CMS\Dashboard\Widgets\RequestAwareWidgetInterface;
use TYPO3\CMS\Dashboard\Widgets\WidgetConfigurationInterface;
use TYPO3\CMS\Dashboard\Widgets\WidgetInterface;

/**
 * Extends original widget with a button provider.
 *
 * Copied from TYPO3\CMS\Dashboard\Widgets\NumberWithIconWidget
 */
class NumberWithIconWidget implements WidgetInterface, RequestAwareWidgetInterface
{
    private ServerRequestInterface $request;

    public function __construct(
        private readonly WidgetConfigurationInterface $configuration,
        private readonly NumberWithIconDataProviderInterface $dataProvider,
        private readonly BackendViewFactory $backendViewFactory,
        private readonly ButtonProviderInterface $buttonProvider,
        private readonly array $options = [],
    ) {
    }

    public function setRequest(ServerRequestInterface $request): void
    {
        $this->request = $request;
    }

    public function renderWidgetContent(): string
    {
        $view = $this->backendViewFactory->create($this->request, ['typo3/cms-dashboard', 'felixnagel/t3extblog']);

        $view->assignMultiple([
            'icon' => $this->options['icon'] ?? '',
            'title' => $this->options['title'] ?? '',
            'subtitle' => $this->options['subtitle'] ?? '',
            'number' => $this->dataProvider->getNumber(),
            'options' => $this->options,
            'configuration' => $this->configuration,
            'button' => $this->buttonProvider,
        ]);

        return $view->render('Widget/NumberWithIconWidget');
    }

    public function getOptions(): array
    {
        return $this->options;
    }
}
