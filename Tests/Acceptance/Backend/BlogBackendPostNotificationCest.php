<?php

declare(strict_types=1);

namespace FelixNagel\T3extblog\Tests\Acceptance\Backend;

/*
 * This file is part of the "t3extblog" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use FelixNagel\T3extblog\Tests\Acceptance\Support\BackendTester;

/**
 * Backend acceptance test for sending "new post" notification mails to blog
 * subscribers (BackendPostController::sendPostNotificationsAction).
 *
 * This covers the full BlogNotificationService::notifySubscribers() flow inside a
 * real backend module context (where the plugin TypoScript — subscription page id,
 * mail addresses, templates — fully resolves), which cannot be triggered from the
 * frontend. The fixture provides confirmed blog subscribers and a post that has
 * not been notified yet (mails_sent = 0).
 */
final class BlogBackendPostNotificationCest
{
    public function _before(BackendTester $I): void
    {
        $I->useExistingSession('admin');
    }

    public function sendingPostNotificationsReportsSuccess(BackendTester $I): void
    {
        // Open the blog module and switch to its "Posts" view (with a page id so the
        // module TypoScript settings are loaded).
        $I->click('Blog', '#modulemenu');
        $I->switchToContentFrame();

        $postsHref = $I->grabAttributeFrom('a[title="Posts"]', 'href');
        $I->executeInSelenium(static function (\Facebook\WebDriver\Remote\RemoteWebDriver $webDriver) use ($postsHref) {
            $webDriver->executeScript('window.location.href = "' . $postsHref . '&id=1"');
        });
        $I->waitForText('First Post', 10);

        // Trigger the "send notifications" action for the first not-yet-notified post.
        // The link carries a confirm() dialog, so grab its href and navigate directly.
        $notifyHref = $I->grabAttributeFrom(
            'a[title="Send new post notification mail to blog subscribers"]',
            'href'
        );
        $I->executeInSelenium(static function (\Facebook\WebDriver\Remote\RemoteWebDriver $webDriver) use ($notifyHref) {
            $webDriver->executeScript('window.location.href = "' . $notifyHref . '"');
        });

        // The action renders the notification mails and redirects back with a flash
        // message reporting how many subscribers were notified.
        $I->waitForText('Successfully sent notification emails', 10);
        $I->dontSee('Oops, an error occurred!');
        $I->dontSee('PHP Fatal error');
    }
}
