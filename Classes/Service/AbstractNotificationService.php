<?php

namespace FelixNagel\T3extblog\Service;

/**
 * This file is part of the "t3extblog" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use FelixNagel\T3extblog\Traits\LoggingTrait;
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Extbase\Object\ObjectManagerInterface;
use TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager;
use TYPO3\CMS\Extbase\SignalSlot\Dispatcher;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;
use FelixNagel\T3extblog\Domain\Model\AbstractSubscriber;
use FelixNagel\T3extblog\Domain\Model\BlogSubscriber;
use FelixNagel\T3extblog\Domain\Model\Comment;
use FelixNagel\T3extblog\Domain\Model\PostSubscriber;

/**
 * Handles all notification mails.
 */
abstract class AbstractNotificationService implements NotificationServiceInterface, SingletonInterface
{
    use LoggingTrait;

    /**
     * @var ObjectManagerInterface
     * @TYPO3\CMS\Extbase\Annotation\Inject
     */
    protected $objectManager;

    /**
     * @var Dispatcher
     * @TYPO3\CMS\Extbase\Annotation\Inject
     */
    protected $signalSlotDispatcher;

    /**
     * subscriberRepository.
     */
    protected $subscriberRepository;

    /**
     * @var \FelixNagel\T3extblog\Service\SettingsService
     * @TYPO3\CMS\Extbase\Annotation\Inject
     */
    protected $settingsService;

    /**
     * @var array
     */
    protected $settings;

    /**
     * @var array
     */
    protected $subscriptionSettings;

    /**
     * @var \FelixNagel\T3extblog\Service\EmailService
     * @TYPO3\CMS\Extbase\Annotation\Inject
     */
    protected $emailService;

    /**
     * @var \FelixNagel\T3extblog\Service\FlushCacheService
     * @TYPO3\CMS\Extbase\Annotation\Inject
     */
    protected $cacheService;

    /**
     * AbstractNotificationService constructor.
     *
     * @param ObjectManagerInterface $objectManager
     * @param Dispatcher $signalSlotDispatcher
     * @param SettingsService $settingsService
     * @param EmailService $emailService
     * @param FlushCacheService $cacheService
     */
    public function __construct(
        ObjectManagerInterface $objectManager,
        Dispatcher $signalSlotDispatcher,
        SettingsService $settingsService,
        EmailService $emailService,
        FlushCacheService $cacheService
    ) {
        $this->objectManager = $objectManager;
        $this->signalSlotDispatcher = $signalSlotDispatcher;
        $this->settingsService = $settingsService;
        $this->emailService = $emailService;
        $this->cacheService = $cacheService;
    }

    /**
     * @inheritDoc
     */
    public function initializeObject()
    {
        $this->settings = $this->settingsService->getTypoScriptSettings();
    }

    /**
     * Send subscriber notification emails.
     *
     * @param AbstractSubscriber $subscriber
     * @param string             $subject
     * @param string             $template
     * @param array              $variables
     */
    protected function sendSubscriberEmail(AbstractSubscriber $subscriber, $subject, $template, $variables = [])
    {
        $defaultVariables = [
            'subscriber' => $subscriber,
            'validUntil' => $this->getValidUntil(),
        ];

        if ($subscriber instanceof BlogSubscriber) {
            $defaultVariables['languageUid'] = $subscriber->getSysLanguageUid();
        }
        if ($subscriber instanceof PostSubscriber) {
            $defaultVariables['languageUid'] = $subscriber->getPost()->getSysLanguageUid();
        }

        $this->sendEmail(
            $subscriber->getMailTo(),
            $subject,
            $template,
            $this->subscriptionSettings['subscriber'],
            array_merge($defaultVariables, $variables)
        );
    }

    /**
     * Send notification emails.
     *
     * @param array $mailTo
     * @param string $subject
     * @param string $template
     * @param array $settings
     * @param array $variables
     */
    protected function sendEmail($mailTo, $subject, $template, $settings, $variables = [])
    {
        $this->emailService->sendEmail(
            $mailTo,
            [$settings['mailFrom']['email'] => $settings['mailFrom']['name']],
            $subject,
            // General language uid: fallback to default
            array_merge(['languageUid' => 0], $variables),
            $template
        );
    }

    /**
     * Render dateTime object for using in template.
     *
     * @return \DateTime
     */
    protected function getValidUntil()
    {
        $date = new \DateTime();
        $modify = '+1 hour';

        if (isset($this->subscriptionSettings['subscriber']['emailHashTimeout'])) {
            $modify = trim($this->subscriptionSettings['subscriber']['emailHashTimeout']);
        }

        $date->modify($modify);

        return $date;
    }

    /**
     * Translate helper.
     *
     * @param string $key      Translation key
     * @param string $variable Argument for translation
     *
     * @return string
     */
    protected function translate($key, $variable = '')
    {
        return LocalizationUtility::translate(
            $key,
            'T3extblog',
            [
                $this->settings['blogName'],
                $variable,
            ]
        );
    }

    /**
     * Helper function for flush frontend page cache.
     *
     * Needed as we want to make sure new comments are visible after enabling in BE.
     * In addition, this clears the cache when a new comment is added in FE.
     *
     * @param Comment $comment Comment
     */
    public function flushFrontendCache($comment)
    {
        $this->cacheService->addCacheTagsToFlush([
            'tx_t3blog_post_uid_'.$comment->getPost()->getLocalizedUid(),
            'tx_t3blog_com_pid_'.$comment->getPid(),
        ]);
    }

    /**
     * Helper function for persisting all changed data to the DB.
     *
     * Needed as in non FE controller context (aka our hook) there is no
     * auto persisting.
     *
     * @param bool $force
     */
    protected function persistToDatabase($force = false)
    {
        if ($force === true || TYPO3_MODE === 'BE') {
            $this->objectManager->get(PersistenceManager::class)->persistAll();
        }
    }
}
