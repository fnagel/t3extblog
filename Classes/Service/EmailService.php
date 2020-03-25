<?php

namespace FelixNagel\T3extblog\Service;

/**
 * This file is part of the "t3extblog" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use FelixNagel\T3extblog\Traits\LoggingTrait;
use TYPO3\CMS\Core\Mail\MailMessage;
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\MailUtility;
use TYPO3\CMS\Extbase\Object\ObjectManagerInterface;
use TYPO3\CMS\Extbase\SignalSlot\Dispatcher;
use TYPO3\CMS\Fluid\View\StandaloneView;

/**
 * Handles email sending and templating.
 */
class EmailService implements SingletonInterface
{
    use LoggingTrait;

    const TEMPLATE_FOLDER = 'Email';

    /**
     * Extension name.
     *
     * @var string
     */
    protected $extensionName = 't3extblog';

    /**
     * @var ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * @var Dispatcher
     */
    protected $signalSlotDispatcher;

    /**
     * @var SettingsService
     */
    protected $settingsService;

    /**
     * @var array
     */
    protected $settings;

    /**
     * EmailService constructor.
     * @param ObjectManagerInterface $objectManager
     * @param Dispatcher $signalSlotDispatcher
     * @param SettingsService $settingsService
     */
    public function __construct(
        ObjectManagerInterface $objectManager,
        Dispatcher $signalSlotDispatcher,
        SettingsService $settingsService
    ) {
        $this->objectManager = $objectManager;
        $this->signalSlotDispatcher = $signalSlotDispatcher;
        $this->settingsService = $settingsService;
    }

    /**
     */
    public function initializeObject()
    {
        $this->settings = $this->settingsService->getTypoScriptSettings();
    }

    /**
     * This is the main-function for sending Mails.
     *
     * @param array  $mailTo
     * @param array  $mailFrom
     * @param string $subject
     * @param array  $variables
     * @param string $templatePath
     *
     * @return int the number of recipients who were accepted for delivery
     */
    public function sendEmail($mailTo, $mailFrom, $subject, $variables, $templatePath)
    {
        $this->signalSlotDispatcher->dispatch(
            __CLASS__,
            'sendEmail',
            [&$mailTo, &$mailFrom, &$subject, &$variables, &$templatePath, $this]
        );

        return $this->send($mailTo, $mailFrom, $subject, $this->render($variables, $templatePath));
    }

    /**
     * This is the main-function for sending Mails.
     *
     * @param array  $mailTo
     * @param array  $mailFrom
     * @param string $subject
     * @param string $emailBody
     *
     * @return int the number of recipients who were accepted for delivery
     */
    public function send($mailTo, $mailFrom, $subject, $emailBody)
    {
        if (!($mailTo && is_array($mailTo) && GeneralUtility::validEmail(key($mailTo)))) {
            $this->getLog()->error('Given mailto email address is invalid.', $mailTo);

            return false;
        }

        if (!($mailFrom && is_array($mailFrom) && GeneralUtility::validEmail(key($mailFrom)))) {
            $mailFrom = MailUtility::getSystemFrom();
        }

        $message = $this->createMailMessage();
        $message
            ->setSubject($subject)
            ->setTo($mailTo)
            ->setFrom($mailFrom);

        $this->setMessageContent($message, $emailBody);

        if (!$this->settings['debug']['disableEmailTransmission']) {
            $message->send();
        }

        $logData = [
            'mailTo' => $mailTo,
            'mailFrom' => $mailFrom,
            'subject' => $subject,
            'emailBody' => $emailBody,
            'isSent' => $message->isSent(),
        ];
        $this->getLog()->dev('Email sent.', $logData);

        return $logData['isSent'];
    }

    /**
     * This functions renders template to use in Mails and Other views.
     *
     * @param array  $variables    Arguments for template
     * @param string $templatePath Choose a template
     *
     * @return string
     */
    public function render($variables, $templatePath = 'Default.txt')
    {
        $emailView = $this->getEmailView($templatePath);
        $emailView->assignMultiple($variables);
        $emailView->assignMultiple([
            'timestamp' => $GLOBALS['EXEC_TIME'],
            'domain' => GeneralUtility::getIndpEnv('TYPO3_SITE_URL'),
            'settings' => $this->settings,
        ]);

        return $emailView->render();
    }

    /**
     * Create and configure the view.
     *
     * @param string $templateFile Choose a template
     *
     * @return StandaloneView
     */
    public function getEmailView($templateFile)
    {
        $emailView = $this->createStandaloneView();

        $format = pathinfo($templateFile, PATHINFO_EXTENSION);
        $emailView->setFormat($format);
        $emailView->getTemplatePaths()->setFormat($format);

        $emailView->getRenderingContext()->setControllerName(self::TEMPLATE_FOLDER);
        $emailView->setTemplate($templateFile);

        return $emailView;
    }

    /**
     * @return StandaloneView
     */
    protected function createStandaloneView()
    {
        /* @var $emailView StandaloneView */
        $emailView = $this->objectManager->get(StandaloneView::class);
        $emailView->getRequest()->setPluginName('');
        $emailView->getRequest()->setControllerExtensionName($this->extensionName);

        $this->setViewPaths($emailView);

        return $emailView;
    }

    /**
     * @param \TYPO3\CMS\Fluid\View\StandaloneView $emailView
     */
    protected function setViewPaths($emailView)
    {
        $frameworkConfig = $this->settingsService->getFrameworkSettings();

        if (isset($frameworkConfig['view']['layoutRootPaths'])) {
            $emailView->setLayoutRootPaths($frameworkConfig['view']['layoutRootPaths']);
        }
        if (isset($frameworkConfig['view']['partialRootPaths'])) {
            $emailView->setPartialRootPaths($frameworkConfig['view']['partialRootPaths']);
        }
        if (isset($frameworkConfig['view']['templateRootPaths'])) {
            $emailView->setTemplateRootPaths($frameworkConfig['view']['templateRootPaths']);
        }
    }

    /**
     * Prepare html as plain text.
     *
     * @param string $html
     *
     * @return string
     */
    protected function preparePlainTextBody($html)
    {
        // Remove style tags
        $output = preg_replace('/<style\\b[^>]*>(.*?)<\\/style>/s', '', $html);

        // Remove tags and extract url from link tags
        $output = strip_tags(preg_replace('/<a.* href=(?:"|\')(.*)(?:"|\').*>/', '$1', $output));

        // Break lines and clean up white spaces
        $output = MailUtility::breakLinesForEmail($output);
        $output = preg_replace('/(?:(?:\r\n|\r|\n)\s*){2}/s', "\n\n", $output);

        return $output;
    }

    /**
     * @param MailMessage $message
     * @param string $emailBody
     */
    protected function setMessageContent(MailMessage $message, $emailBody)
    {
        // Plain text only
        if (strip_tags($emailBody) == $emailBody) {
            $message->text($emailBody);
        } else {
            // Send as HTML and plain text
            $message->html($emailBody);
            $message->text($this->preparePlainTextBody($emailBody));
        }
    }

    /**
     * Create mail message
     *
     * @return MailMessage
     */
    protected function createMailMessage()
    {
        return $this->objectManager->get(MailMessage::class);
    }
}
