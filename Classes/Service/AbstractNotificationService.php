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
use FelixNagel\T3extblog\Utility\SiteUtility;
use Psr\EventDispatcher\EventDispatcherInterface;
use TYPO3\CMS\Core\Localization\Locale;
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Site\Entity\SiteLanguage;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;
use FelixNagel\T3extblog\Domain\Model\AbstractSubscriber;
use FelixNagel\T3extblog\Domain\Model\Comment;
use TYPO3\CMS\Core\Http\ApplicationType;

/**
 * Handles all notification mails.
 *
 * @SuppressWarnings("PHPMD.CouplingBetweenObjects")
 */
abstract class AbstractNotificationService implements NotificationServiceInterface, SingletonInterface
{
    use LoggingTrait;

    protected $subscriberRepository;

    protected array $settings = [];

    protected array $subscriptionSettings = [];

    public function __construct(
        protected SettingsService $settingsService,
        protected EmailService $emailService,
        protected FlushCacheService $cacheService,
        protected readonly EventDispatcherInterface $eventDispatcher
    ) {

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
        array $variables = [],
    ): void {
        if (empty($subscriber->getEmail())) {
            throw new Exception('Email address is a required property!', 1592248953);
        }

        $this->sendEmail(
            $subscriber->getMailTo(),
            $subject,
            $template,
            $this->subscriptionSettings['subscriber'],
            array_merge($variables, [
                'subscriber' => $subscriber,
                'validUntil' => $this->getValidUntil(),
            ]),
            SiteUtility::getLanguage($subscriber->getPid(), $subscriber->getSysLanguageUid())
        );
    }

    /**
     * Send notification emails.
     */
    protected function sendEmail(
        array $mailTo,
        string $subject,
        string $template,
        array $settings,
        array $variables = [],
        ?SiteLanguage $language = null
    ): void {
        $this->emailService->send(
            $mailTo,
            [$settings['mailFrom']['email'] => $settings['mailFrom']['name']],
            $subject,
            $variables,
            $template,
            $language->getLocale()
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

    protected function translate(string $key, string $variable = '', ?Locale $locale = null): ?string
    {
        return LocalizationUtility::translate(
            $key,
            'T3extblog',
            [
                $this->settings['blogName'],
                $variable,
            ],
            // @todo Using the Local object directly will result in wrong localization!
            // Same issue is true for the Fluid view helpers.
            // Seems to be a core bug, see https://forge.typo3.org/issues/108102
            $locale->getLanguageCode()
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
            GeneralUtility::makeInstance(PersistenceManager::class)->persistAll();
        }
    }
}
