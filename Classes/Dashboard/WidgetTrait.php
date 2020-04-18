<?php

namespace FelixNagel\T3extblog\Dashboard;

/**
 *  This file is part of the "t3extblog" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use FelixNagel\T3extblog\Utility\BlogPageSearchUtility;
use TYPO3\CMS\Backend\Routing\UriBuilder;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Fluid\View\StandaloneView;
use TYPO3Fluid\Fluid\View\TemplatePaths;

trait WidgetTrait
{
    /**
     * @var ObjectManager
     */
    protected $objectManager = null;

    /**
     * @var StandaloneView
     */
    protected $view;

    /**
     * Configure view
     *
     * Override template path for our custom widget
     */
    protected function configureView(): void
    {
        $filepath = GeneralUtility::getFileAbsFileName(
            'EXT:t3extblog/' . TemplatePaths::DEFAULT_TEMPLATES_DIRECTORY . 'Widget/' . $this->templateName . '.html'
        );

        if (file_exists($filepath)) {
            $this->view->setTemplatePathAndFilename($filepath);
        }
    }

    /**
     * Initialize objects and do stuff before view is initialized
     */
    protected function initialize()
    {
        $this->objectManager = GeneralUtility::makeInstance(ObjectManager::class);
    }

    /**
     * @param int $id
     * @param array $arguments
     * @return string
     */
    protected function getModuleLink($id = null, array $arguments = [])
    {
        $parameters = [];
        $uriBuilder = GeneralUtility::makeInstance(UriBuilder::class);

        if ($id === null) {
            $pages = $this->getBlogPageUids();

            if (count($pages) === 1) {
                $id = (int)current($pages);
            }
        }

        if (is_int($id)) {
            $parameters['id'] = $id;
        }

        if (count($arguments) > 0) {
            $parameters['tx_t3extblog_web_t3extblogtxt3extblog'] = $arguments;
        }

        return (string)$uriBuilder->buildUriFromRoute('web_T3extblogTxT3extblog', $parameters);
    }

    /**
     * @todo Add dashboard specific TSconfig based PID configuration
     *
     * @return array
     */
    protected function getBlogPageUids()
    {
        return BlogPageSearchUtility::getBlogPageUids();
    }

    /**
     * @return int
     */
    protected function getStoragePids()
    {
        $pages = $this->getBlogPageUids();

        // @todo Remove this and make repo method able to use multiple PIDs
        return (count($pages) === 1) ? current($pages) : -1;
    }

    /**
     * @param string $key
     * @return string
     */
    protected function translate($key)
    {
        return $this->getLanguageService()->sL(self::LOCALLANG_FILE . $key);
    }
}
