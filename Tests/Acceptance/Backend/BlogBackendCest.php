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
 * Backend acceptance tests for the t3extblog backend module.
 *
 * Covers:
 * - Backend login and module visibility
 * - Comment management (pending comments list)
 *
 * All tests reuse the pre-seeded admin session (no login form required).
 */
final class BlogBackendCest
{
    public function _before(BackendTester $I): void
    {
        $I->useExistingSession('admin');
    }

    /**
     * The blog module appears in the backend module menu after login.
     * The module is registered as a child of "web" and labelled "Blog".
     */
    public function blogModuleIsAccessibleInMenu(BackendTester $I): void
    {
        $I->see('Blog', '#modulemenu');
    }

    /**
     * Clicking the Blog module loads the backend dashboard/index view.
     */
    public function blogModuleDashboardLoads(BackendTester $I): void
    {
        $I->click('Blog', '#modulemenu');
        $I->switchToContentFrame();
        // The backend dashboard should render without errors
        $I->dontSee('Oops, an error occurred!');
        $I->dontSee('PHP Fatal error');
    }

    /**
     * The pending comments list renders with the fixture comment by "Bob"
     * that was submitted but not yet approved.
     */
    public function pendingCommentsListRendersPendingComment(BackendTester $I): void
    {
        $I->click('Blog', '#modulemenu');
        $I->switchToContentFrame();
        // Navigate to the pending comments sub-module with page id=1 so TypoScript is loaded.
        // We grab the href from the link (which has the CSRF token) and navigate via JS
        // to stay within the list_frame context (window.location within iframe scope).
        $href = $I->grabAttributeFrom('a[title="Comments: Pending comments"]', 'href');
        $I->executeInSelenium(static function (\Facebook\WebDriver\Remote\RemoteWebDriver $webDriver) use ($href) {
            $webDriver->executeScript('window.location.href = "' . $href . '&id=1"');
        });
        $I->waitForText('Pending comments');
        // The pending comment by Bob should be visible
        $I->see('Bob');
    }
}
