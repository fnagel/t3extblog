<?php

namespace FelixNagel\T3extblog\Controller;

/**
 * This file is part of the "t3extblog" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use FelixNagel\T3extblog\Traits\LoggingTrait;
use FelixNagel\T3extblog\Utility\TypoScript;
use TYPO3\CMS\Core\Http\ImmediateResponseException;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Messaging\FlashMessage;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Extbase\Mvc\RequestInterface;
use TYPO3\CMS\Extbase\Mvc\ResponseInterface;
use TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;
use FelixNagel\T3extblog\Domain\Model\AbstractEntity;
use FelixNagel\T3extblog\Domain\Model\Category;
use FelixNagel\T3extblog\Domain\Model\Comment;
use FelixNagel\T3extblog\Domain\Model\Post;
use FelixNagel\T3extblog\Utility\TypoScriptValidator;
use TYPO3\CMS\Frontend\Controller\ErrorController;

/**
 * Abstract base controller.
 *
 * @SuppressWarnings("PHPMD.CouplingBetweenObjects")
 */
abstract class AbstractController extends ActionController
{
    use LoggingTrait;

    /**
     * Actions which need a configured cHash
     *
     * @var array
     */
    protected $cHashActions = [];

    /**
     * Injects the Configuration Manager and is initializing the framework settings
     * Function is used to override the merge of settings via TS & flexforms
     * original code taken from http://forge.typo3.org/projects/typo3v4-mvc/wiki/How_to_control_override_of_TS-Flexform_configuration.
     *
     * @param $configurationManager ConfigurationManagerInterface An instance of the Configuration Manager
     */
    public function injectConfigurationManager(ConfigurationManagerInterface $configurationManager)
    {
        $this->configurationManager = $configurationManager;

        $tsSettings = $this->configurationManager->getConfiguration(
            ConfigurationManagerInterface::CONFIGURATION_TYPE_FRAMEWORK,
            't3extblog',
            't3extblog_blogsystem'
        );

        $originalSettings = $this->configurationManager->getConfiguration(
            ConfigurationManagerInterface::CONFIGURATION_TYPE_SETTINGS
        );

        // start override
        if (isset($tsSettings['settings']['overrideFlexformSettingsIfEmpty'])) {
            /** @var TypoScript $typoScriptUtility */
            $typoScriptUtility = GeneralUtility::makeInstance(TypoScript::class);
            $originalSettings = $typoScriptUtility->override($originalSettings, $tsSettings);
        }

        $this->settings = $originalSettings;
    }

    /**
     * @inheritdoc
     */
    public function processRequest(RequestInterface $request, ResponseInterface $response)
    {
        try {
            parent::processRequest($request, $response);
        } catch (\Exception $exception) {
            $this->handleKnownExceptionsElseThrowAgain($exception);
        }
    }

    /**
     * @param \Exception $exception
     * @throws \Exception
     */
    protected function handleKnownExceptionsElseThrowAgain(\Exception $exception)
    {
        throw $exception;
    }

    /**
     * Initializes the controller before invoking an action method.
     *
     * @api
     */
    protected function initializeAction()
    {
        try {
            $this->validateTypoScriptConfiguration();
        } catch (\Exception $exception) {
            $this->getLog()->exception($exception, [
                'pid' => \FelixNagel\T3extblog\Utility\GeneralUtility::getTsFe()->id,
                'context' => 'frontend',
            ]);
            throw $exception;
        }

        $this->addDefaultCacheTags();
        $this->configureCHash();
    }

    /**
     * Validate TypoScript settings.
     *
     * @throw  FelixNagel\T3extblog\Exception\InvalidConfigurationException
     */
    protected function validateTypoScriptConfiguration()
    {
        TypoScriptValidator::validateSettings($this->settings);

        $frameworkConfiguration = $this->configurationManager->getConfiguration(
            \TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface::CONFIGURATION_TYPE_FRAMEWORK,
            $this->request->getControllerExtensionName(),
            $this->request->getPluginName()
        );
        TypoScriptValidator::validateFrameworkConfiguration($frameworkConfiguration);
    }

    /**
     * Override getErrorFlashMessage to present
     * nice flash error messages.
     *
     * @return string
     */
    protected function getErrorFlashMessage()
    {
        $defaultFlashMessage = parent::getErrorFlashMessage();
        $locallangKey = sprintf('error.%s.%s', lcfirst($this->request->getControllerName()), $this->actionMethodName);

        return $this->translate($locallangKey, $defaultFlashMessage);
    }

    /**
     * Helper function to render localized flashmessages.
     *
     * @param string $key
     * @param int    $severity optional severity code. One of the FlashMessage constants
     */
    protected function addFlashMessageByKey($key, $severity = FlashMessage::OK)
    {
        $messageLocallangKey = sprintf('flashMessage.%s.%s', lcfirst($this->request->getControllerName()), $key);
        $localizedMessage = $this->translate($messageLocallangKey, '['.$messageLocallangKey.']');

        $titleLocallangKey = sprintf('%s.title', $messageLocallangKey);
        $localizedTitle = $this->translate($titleLocallangKey, '['.$titleLocallangKey.']');

        $this->addFlashMessage($localizedMessage, $localizedTitle, $severity);
    }

    /**
     * Helper function to check if flashmessages have been saved until now.
     *
     * @return bool
     */
    protected function hasFlashMessages()
    {
        $messages = $this->controllerContext->getFlashMessageQueue()->getAllMessages();

        if (count($messages) > 0) {
            return true;
        }

        return false;
    }

    /**
     * @param string $message
     */
    protected function pageNotFoundAndExit($message = 'Entity not found.')
    {
        $response = GeneralUtility::makeInstance(ErrorController::class)->pageNotFoundAction(
            $GLOBALS['TYPO3_REQUEST'],
            $message
        );

        throw new ImmediateResponseException($response, 1576748646637);
    }

    /**
     * Persist all records to database.
     *
     * @return string
     */
    protected function persistAllEntities()
    {
        /* @var $persistenceManager PersistenceManager */
        $persistenceManager = $this->objectManager->get(PersistenceManager::class);
        $persistenceManager->persistAll();
    }

    /**
     * Helper function to use localized strings in BlogExample controllers.
     *
     * @param string $key            locallang key
     * @param string $defaultMessage the default message to show if key was not found
     *
     * @return string
     */
    protected function translate($key, $defaultMessage = '')
    {
        $message = LocalizationUtility::translate($key, 'T3extblog');

        if ($message === null) {
            $message = $defaultMessage;
        }

        return $message;
    }

    /**
     * Add page cache tag by string or object.
     *
     * @param mixed $object A cache tag string or a blog model object
     */
    protected function addCacheTags($object = null)
    {
        if (TYPO3_MODE !== 'FE') {
            return;
        }

        $tags = is_array($object) ? $object : [];

        if (is_string($object)) {
            $tags[] = $object;
        }

        // Add base PID based tag
        if ($object instanceof AbstractEntity) {
            $tags[] = 'tx_t3extblog_'.$object->getPid();
        }

        // Add model based tag
        if ($object instanceof Post) {
            $tags[] = 'tx_t3blog_post_pid_'.$object->getPid();
        }
        if ($object instanceof Comment) {
            $tags[] = 'tx_t3blog_com_pid_'.$object->getPid();
        }
        if ($object instanceof Category) {
            $tags[] = 'tx_t3blog_cat_pid_'.$object->getPid();
        }

        \FelixNagel\T3extblog\Utility\GeneralUtility::getTsFe()->addCacheTags($tags);
    }

    /**
     * Add basic cache tag for all related pages.
     */
    protected function addDefaultCacheTags()
    {
        static $cacheTagsSet = false;

        if ($cacheTagsSet === false) {
            $this->addCacheTags(['tx_t3extblog']);

            // We only want to set the tag once in one request
            $cacheTagsSet = true;
        }
    }

    /**
     * Add basic cache tag for all related pages.
     *
     * @deprecated
     * @todo Seems to be no longer needed in TYPO3 10
     */
    protected function configureCHash()
    {
        if (count($this->cHashActions) > 0 && in_array($this->actionMethodName, $this->cHashActions)) {
            \FelixNagel\T3extblog\Utility\GeneralUtility::getTsFe()->reqCHash();
        }
    }
}
