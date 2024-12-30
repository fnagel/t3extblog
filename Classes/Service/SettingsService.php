<?php

namespace FelixNagel\T3extblog\Service;

/**
 * This file is part of the "t3extblog" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\Http\ApplicationType;
use TYPO3\CMS\Core\TypoScript\TypoScriptService;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use TYPO3\CMS\Extbase\Configuration\Exception;
use TYPO3\CMS\Extbase\Reflection\ObjectAccess;

/**
 * Provide a way to get the configuration just everywhere.
 */
class SettingsService
{
    /**
     * Extension name.
     *
     * Needed as parameter for configurationManager->getConfiguration when used in BE context
     * Otherwise generated TS will be incorrect or missing
     */
    protected string $extensionName = 'T3extblog';

    /**
     * Plugin name.
     *
     * Needed as parameter for configurationManager->getConfiguration when used in BE context
     * Otherwise generated TS will be incorrect or missing when used in BE
     */
    protected string $pluginName = '';

    protected ?array $typoScriptSettings = null;

    protected ?array $frameworkSettings = null;

    /**
     * Make sure TS in BE context is generated from currently selected page.
     *
     * Needed basically to make extbase work in some situations. Without PID is set to 0
     * (which is root) and persistence, TS generation, etc. will fail. This is the case
     * in TYPO3 8-13 when editing a record.
     *
     * Example: right-click on a record using the context menu in the blog (or list)
     * BE module and use edit (tab access, change to valid and click save) or change
     * visibility directly in context menu which should trigger emails but does not.
     */
    public function __construct(protected ConfigurationManagerInterface $configurationManager, protected TypoScriptService $typoScriptService)
    {
        if (isset($GLOBALS['TYPO3_REQUEST']) && ($request = $GLOBALS['TYPO3_REQUEST']) instanceof ServerRequestInterface &&
            !ApplicationType::fromRequest($request)->isFrontend()
        ) {
            $this->configurationManager->setRequest($request->withQueryParams([
                ...$request->getQueryParams(),
                ...['id' => ($request->getParsedBody()['popViewId'] ?? $request->getQueryParams()['popViewId'] ?? 0)]
            ]));
        }
    }

    /**
     * Returns all framework settings.
     */
    public function getFrameworkSettings(): array
    {
        if ($this->frameworkSettings === null) {
            $this->frameworkSettings = $this->configurationManager->getConfiguration(
                ConfigurationManagerInterface::CONFIGURATION_TYPE_FRAMEWORK,
                $this->extensionName,
                $this->pluginName
            );
        }

        if ($this->frameworkSettings === null) {
            throw new Exception('No framework TypoScript configuration available!', 1592249266);
        }

        return $this->frameworkSettings;
    }

    /**
     * Returns all TS settings.
     */
    public function getTypoScriptSettings(): array
    {
        if ($this->typoScriptSettings === null) {
            $this->typoScriptSettings = $this->configurationManager->getConfiguration(
                ConfigurationManagerInterface::CONFIGURATION_TYPE_SETTINGS,
                $this->extensionName,
                $this->pluginName
            );
        }

        if ($this->typoScriptSettings === null) {
            throw new Exception('No settings TypoScript configuration available!', 1592249324);
        }

        return $this->typoScriptSettings;
    }

    /**
     * Returns the settings at path $path, which is separated by ".",
     * e.g. "pages.uid".
     * "pages.uid" would return $this->settings['pages']['uid'].
     *
     * If the path is invalid or no entry is found, false is returned.
     */
    public function getTypoScriptByPath(string $path)
    {
        return ObjectAccess::getPropertyPath($this->getTypoScriptSettings(), $path);
    }
}
