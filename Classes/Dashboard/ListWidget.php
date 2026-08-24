<?php

namespace FelixNagel\T3extblog\Dashboard;

/**
 * This file is part of the "t3extblog" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use FelixNagel\T3extblog\Dashboard\Provider\AbstractListDataProvider;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Backend\View\BackendViewFactory;
use TYPO3\CMS\Core\Settings\SettingDefinition;
use TYPO3\CMS\Dashboard\Widgets\ButtonProviderInterface;
use TYPO3\CMS\Dashboard\Widgets\ListDataProviderInterface;
use TYPO3\CMS\Dashboard\Widgets\RequestAwareWidgetInterface;
use TYPO3\CMS\Dashboard\Widgets\WidgetConfigurationInterface;
use TYPO3\CMS\Dashboard\Widgets\WidgetContext;
use TYPO3\CMS\Dashboard\Widgets\WidgetRendererInterface;
use TYPO3\CMS\Dashboard\Widgets\WidgetResult;

/**
 * Extends original widget with a configurable item limit.
 *
 * Copied from TYPO3\CMS\Dashboard\Widgets\ListWidget
 */
class ListWidget implements WidgetRendererInterface, RequestAwareWidgetInterface
{
    protected ServerRequestInterface $request;

    public function __construct(
        private readonly WidgetConfigurationInterface $configuration,
        private readonly ListDataProviderInterface $dataProvider,
        private readonly BackendViewFactory $backendViewFactory,
        private readonly ?ButtonProviderInterface $buttonProvider = null,
        /** @var array{partial?: string, table?: string, limit?: int} */
        private readonly array $options = [],
    ) {
    }

    public function setRequest(ServerRequestInterface $request): void
    {
        $this->request = $request;
    }

    public function getSettingsDefinitions(): array
    {
        return [
            new SettingDefinition(
                key: 'limit',
                type: 'int',
                default: (int)($this->options['limit'] ?? 10),
                label: 'LLL:EXT:t3extblog/Resources/Private/Language/locallang_dashboard.xlf:widget.setting.limit.label',
                description: 'LLL:EXT:t3extblog/Resources/Private/Language/locallang_dashboard.xlf:widget.setting.limit.description',
            ),
        ];
    }

    public function renderWidget(WidgetContext $context): WidgetResult
    {
        if ($this->dataProvider instanceof AbstractListDataProvider) {
            $this->dataProvider->setOptions(['limit' => (int)$context->settings->get('limit')]);
        }

        $view = $this->backendViewFactory->create($context->request);
        $view->assignMultiple([
            'items' => $this->dataProvider->getItems(),
            'options' => $this->options,
            'button' => $this->buttonProvider,
            'configuration' => $this->configuration,
        ]);

        return new WidgetResult(
            content: $view->render('Widget/ListWidget'),
            refreshable: true,
        );
    }

    public function getOptions(): array
    {
        return $this->options;
    }
}
