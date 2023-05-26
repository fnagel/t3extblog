<?php

namespace FelixNagel\T3extblog\Service;

/**
 * This file is part of the "t3extblog" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use FelixNagel\T3extblog\Exception\Exception;
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
use TYPO3\CMS\Core\Http\ApplicationType;

/**
 * Handles all notification mails.
 *
 * @SuppressWarnings("PHPMD.CouplingBetweenObjects")
 */
abstract class AbstractNotificationService implements NotificationServiceInterface, SingletonInterface
{
    use LoggingTrait;

    protected ObjectManagerInterface $objectManager;

    protected Dispatcher $signalSlotDispatcher;

    protected $subscriberRepository;

    protected array $settings = [];

    protected array $subscriptionSettings = [];

    public function __construct(
        ObjectManagerInterface $objectManager,
        Dispatcher $signalSlotDispatcher,
        protected SettingsService $settingsService,
        protected EmailService $emailService,
        protected FlushCacheService $cacheService
    ) {
        $this->objectManager = $objectManager;
        $this->signalSlotDispatcher = $signalSlotDispatcher;
    }

    public function initializeObject()
    {
        $this->settings = $this->settingsService->getTypoScriptSettings();
    }

    /**
     * Send subscriber notification emails.
     */
    protected function sendSubscriberEmail(
        AbstractSubscriber $subscriber,
        string $subject,
        string $template,
        array $variables = []
    ) {
        if (empty($subscriber->getEmail())) {
            throw new Exception('Email address is a required property!', 1592248953);
        }

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
     */
    protected function sendEmail(array $mailTo, string $subject, string $template, array $settings, array $variables = [])
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
     */
    protected function getValidUntil(): \DateTime
    {
        $date = new \DateTime();
        $modify = '+1 hour';

        if (isset($this->subscriptionSettings['subscriber']['emailHashTimeout'])) {
            $modify = trim($this->subscriptionSettings['subscriber']['emailHashTimeout']);
        }

        $date->modify($modify);

        return $date;
    }

    protected function translate(string $key, string $variable = ''): ?string
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
    public function flushFrontendCache(Comment $comment)
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
     */
    protected function persistToDatabase(bool $force = false)
    {
        if ($force || ApplicationType::fromRequest($GLOBALS['TYPO3_REQUEST'])->isBackend()) {
            $this->objectManager->get(PersistenceManager::class)->persistAll();
        }
    }
}
