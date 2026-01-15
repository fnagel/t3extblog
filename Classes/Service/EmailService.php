<?php

namespace FelixNagel\T3extblog\Service;

/**
 * This file is part of the "t3extblog" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use FelixNagel\T3extblog\Mail\EmailWithViewInterface;
use FelixNagel\T3extblog\Mail\FluidEmail;
use FelixNagel\T3extblog\Mail\MailMessage;
use FelixNagel\T3extblog\Traits\LoggingTrait;
use FelixNagel\T3extblog\Event;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\Http\Message\ServerRequestInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;
use TYPO3\CMS\Core\Localization\Locale;
use TYPO3\CMS\Core\Localization\Locales;
use TYPO3\CMS\Core\Mail\MailerInterface;
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\MailUtility;
use TYPO3\CMS\Core\View\ViewInterface;
use TYPO3\CMS\Extbase\Mvc\ExtbaseRequestParameters;
use TYPO3\CMS\Extbase\Mvc\Request;
use TYPO3\CMS\Core\Context\Context;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;

/**
 * Handles email sending and templating.
 *
 * @SuppressWarnings("PHPMD.CouplingBetweenObjects")
 */
class EmailService implements SingletonInterface
{
    use LoggingTrait;

    /**
     * @var string
     */
    public const TEMPLATE_FOLDER = 'Email';

    /**
     * Extension name.
     */
    protected string $extensionName = 't3extblog';

    protected array $settings = [];

    /**
     * EmailService constructor.
     */
    public function __construct(
        protected SettingsService $settingsService,
        protected MailerInterface $mailer,
        protected Locales $locales,
        protected readonly EventDispatcherInterface $eventDispatcher
    ) {
    }

    public function initializeObject()
    {
        $this->settings = $this->settingsService->getTypoScriptSettings();
    }

    /**
     * This is the function for sending Mails using a template name.
     */
    // @extensionScannerIgnoreLine
    public function send(
        array $mailTo,
        array $mailFrom,
        string $subject,
        array $variables,
        string $templatePath,
        ?Locale $locale = null
    ): bool {
        /** @var Event\SendEmailEvent $event */
        $event = $this->eventDispatcher->dispatch(
            new Event\SendEmailEvent($mailTo, $mailFrom, $subject, $variables, $templatePath)
        );
        $mailTo = $event->getMailTo();
        $mailFrom = $event->getMailFrom();
        $subject = $event->getSubject();
        $variables = $event->getVariables();
        $templatePath = $event->getTemplatePath();

        if (!GeneralUtility::validEmail(key($mailTo))) {
            // @extensionScannerIgnoreLine
            $this->getLog()->error('Given mailto email address is invalid.', $mailTo);

            return false;
        }

        $message = $this->getMessage($templatePath, $variables, $locale);
        $message
            ->subject($subject)
            ->to(new Address(key($mailFrom), current($mailFrom) ?? ''))
            ->from(new Address(
                GeneralUtility::validEmail(key($mailFrom)) ? key($mailFrom) : MailUtility::getSystemFromAddress(),
                current($mailFrom) ?? MailUtility::getSystemFromName() ?? ''
            ));

        if (!$this->settings['debug']['disableEmailTransmission']) {
            $this->mailer->send($message);
        }

        $logData = [
            'mailTo' => $mailTo,
            'mailFrom' => $mailFrom,
            'subject' => $subject,
            'emailBody' => $message->getBody(),
            'isSent' => $this->mailer->getSentMessage() !== null,
        ];
        $this->getLog()->dev('Email sent.', $logData);

        return $logData['isSent'];
    }

    protected function getMessage(string $template, array $variables, ?Locale $locale = null): Email
    {
        $message = $this->createMessage($template);

        if ($message instanceof EmailWithViewInterface) {
            $message->setRequest($this->creatRequest());

            $context = $message->getRenderingContext();
            $context->setControllerName(self::TEMPLATE_FOLDER);
            $context->setControllerAction($template);
        }

        if ($message instanceof ViewInterface) {
            $message->assignMultiple($variables);
            $message->assignMultiple([
                'timestamp' => GeneralUtility::makeInstance(Context::class)->getPropertyFromAspect('date', 'timestamp'),
                'domain' => GeneralUtility::getIndpEnv('TYPO3_SITE_URL'),
                'settings' => $this->settings,
                'locale' => $locale ?? $this->locales->createLocale('default'),
                // Layout for templating
                'layout' => $message->getLayout(),
            ]);
        }

        return $message;
    }

    protected function createMessage(string $template): Email
    {
        $settings = $this->settingsService->getFrameworkSettings();

        return GeneralUtility::makeInstance(
            $settings['email']['type'] === 'fluidEmail' ? FluidEmail::class : MailMessage::class,
            $template,
            $settings
        );
    }

    protected function creatRequest(): ServerRequestInterface
    {
        $extbaseAttribute = new ExtbaseRequestParameters();
        $extbaseAttribute->setControllerExtensionName($this->extensionName);

        /* @var $request ServerRequestInterface */
        $request = $GLOBALS['TYPO3_REQUEST']->withAttribute('extbase', $extbaseAttribute);

        if ($request->getAttribute('currentContentObject') === null) {
            $request = $request->withAttribute(
                'currentContentObject',
                GeneralUtility::makeInstance(ContentObjectRenderer::class)
            );
        }

        return (new Request($request));
    }
}
