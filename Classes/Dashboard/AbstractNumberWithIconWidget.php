<?php

namespace FelixNagel\T3extblog\Dashboard;

/**
 * This file is part of the "t3extblog" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use TYPO3\CMS\Dashboard\Widgets\AbstractNumberWithIconWidget as CoreAbstracWidget;

abstract class AbstractNumberWithIconWidget extends CoreAbstracWidget
{
    use WidgetTrait;

    const LOCALLANG_FILE = 'LLL:EXT:t3extblog/Resources/Private/Language/locallang_dashboard.xlf:';

    /**
     * @inheritDoc
     */
    protected $icon = 'extensions-t3extblog-plugin';

    /**
     * @inheritDoc
     */
    protected $templateName = 'NumberWithIconWidget';

    /**
     * @var string
     */
    protected $link = null;

    /**
     * @inheritDoc
     */
    public function __construct(string $identifier)
    {
        $this->initialize();

        parent::__construct($identifier);
    }

    /**
     * @inheritDoc
     */
    protected function initializeView(): void
    {
        $this->updateNumber();

        parent::initializeView();

        $this->configureView();

        $this->view->assign('link', $this->link);
    }

    /**
     * Return number to display
     *
     * @return void
     */
    abstract protected function updateNumber();
}
