<?php

namespace FelixNagel\T3extblog\Service;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2013-2018 Felix Nagel <info@felixnagel.com>
 *
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;
use FelixNagel\T3extblog\Domain\Model\AbstractSubscriber;
use FelixNagel\T3extblog\Domain\Model\BlogSubscriber;
use FelixNagel\T3extblog\Domain\Model\Comment;
use FelixNagel\T3extblog\Domain\Model\Post;
use FelixNagel\T3extblog\Domain\Model\PostSubscriber;

/**
 * Handles all notification mails.
 */
abstract class AbstractNotificationService implements NotificationServiceInterface, SingletonInterface
{
    /**
     * @var \TYPO3\CMS\Extbase\Object\ObjectManagerInterface
     * @TYPO3\CMS\Extbase\Annotation\Inject
     */
    protected $objectManager;

    /**
     * subscriberRepository.
     */
    protected $subscriberRepository;

    /**
     * Logging Service.
     *
     * @var \FelixNagel\T3extblog\Service\LoggingServiceInterface
     * @TYPO3\CMS\Extbase\Annotation\Inject
     */
    protected $log;

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
     * @var \TYPO3\CMS\Extbase\SignalSlot\Dispatcher
     * @TYPO3\CMS\Extbase\Annotation\Inject
     */
    protected $signalSlotDispatcher;

    /**
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
     * @todo We probably want to move this back to Fluid
     *       Using a format:date VH stopped working with 7.4
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
