<?php

namespace FelixNagel\T3extblog\Dashboard;

/**
 * This file is part of the "t3extblog" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use TYPO3\CMS\Dashboard\Widgets\AbstractListWidget as CoreAbstracWidget;

abstract class AbstractListWidget extends CoreAbstracWidget
{
    use WidgetTrait;

    const LOCALLANG_FILE = 'LLL:EXT:t3extblog/Resources/Private/Language/locallang_dashboard.xlf:';

    /**
     * @inheritDoc
     */
    public function __construct(string $identifier)
    {
        parent::__construct($identifier);

        $this->initialize();
    }

    /**
     * @inheritDoc
     */
    protected function initializeView(): void
    {
        parent::initializeView();

        $this->configureView();
    }

    /**
     * @param array $arguments
     */
    protected function setMoreItemsLink(array $arguments)
    {
        $this->moreItemsLink = $this->getModuleLink(null, $arguments);
    }

    /**
     * @return array
     */
    abstract protected function getListItems();
}
