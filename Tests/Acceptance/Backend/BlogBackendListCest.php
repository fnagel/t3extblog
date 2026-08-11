<?php

declare(strict_types=1);

namespace FelixNagel\T3extblog\Tests\Acceptance\Backend;

/*
 * This file is part of the "t3extblog" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use Facebook\WebDriver\Remote\RemoteWebDriver;
use FelixNagel\T3extblog\Tests\Acceptance\Support\BackendTester;

/**
 * Backend acceptance tests for the t3extblog module list views and their pagination.
 *
 * Covers (all list views use itemsPerPage = 5, and the fixtures provide 7 records
 * each, so a second page must exist):
 * - The backend dashboard/index view renders its sections
 * - The "All comments" list + pagination
 * - The "New post subscriptions" (blog subscriber) list + pagination
 * - The "New comment subscriptions" (post subscriber) list + pagination
 *
 * All tests reuse the pre-seeded admin session (no login form required).
 */
final class BlogBackendListCest
{
    public function _before(BackendTester $I): void
    {
        $I->useExistingSession('admin');
    }

    /**
     * The module index (backend dashboard) renders its main sections without errors.
     */
    public function dashboardIndexRendersSections(BackendTester $I): void
    {
        // Navigate to the dashboard with a page id so its TypoScript settings load.
        $this->navigateToMenuItem($I, 'Dashboard');
        $I->waitForText('Blog statistic', 10);

        $I->dontSee('Oops, an error occurred!');
        $I->dontSee('PHP Fatal error');
        $I->see('Latest comments');
        $I->see('Latest subscriptions');
    }

    /**
     * The "All comments" list shows the fixture comments and paginates them.
     */
    public function commentListPaginates(BackendTester $I): void
    {
        $this->openModuleView($I, 'Comments: All comments');

        // The comment list renders with a working pagination bar (12 comments).
        $I->seeElement('.pagination');
        $this->seePageTwoThenNavigate($I);
    }

    /**
     * The "New post subscriptions" (blog subscriber) list paginates.
     */
    public function blogSubscriberListPaginates(BackendTester $I): void
    {
        $this->openModuleView($I, 'Subscriptions: New post subscriptions');

        // The blog subscriber list renders subscriber emails and a pagination bar.
        $I->seeElement('.pagination');
        $I->see('@example.com');
        $this->seePageTwoThenNavigate($I);
    }

    /**
     * The "New comment subscriptions" (post subscriber) list paginates.
     */
    public function commentSubscriberListPaginates(BackendTester $I): void
    {
        $this->openModuleView($I, 'Subscriptions: New comment subscriptions');

        // The comment subscriber list renders subscriber emails and a pagination bar.
        $I->seeElement('.pagination');
        $I->see('@example.com');
        $this->seePageTwoThenNavigate($I);
    }

    /**
     * Open the blog backend module (defaults to the dashboard/index view).
     */
    protected function openBlogModule(BackendTester $I): void
    {
        $I->click('Blog', '#modulemenu');
        $I->switchToContentFrame();
    }

    /**
     * Open a specific module view via its docheader menu entry.
     *
     * The menu links carry a CSRF token but no page id, so the id is appended and
     * the navigation happens inside the module iframe (matching BlogBackendCest).
     */
    protected function openModuleView(BackendTester $I, string $menuTitle): void
    {
        $this->navigateToMenuItem($I, $menuTitle);
        $I->waitForElement('.pagination', 10);
    }

    /**
     * Navigate to a module docheader menu entry, adding the page id (needed so the
     * module's TypoScript settings are loaded) and staying inside the module iframe.
     */
    protected function navigateToMenuItem(BackendTester $I, string $menuTitle): void
    {
        $this->openBlogModule($I);

        $href = $I->grabAttributeFrom('a[title="' . $menuTitle . '"]', 'href');
        $I->executeInSelenium(static function (RemoteWebDriver $webDriver) use ($href) {
            $webDriver->executeScript('window.location.href = "' . $href . '&id=1"');
        });
    }

    /**
     * Assert a second page link is present, navigate to it and verify it is active.
     */
    protected function seePageTwoThenNavigate(BackendTester $I): void
    {
        // A "2" page link exists and the "next" control is not disabled.
        $I->seeElement('.pagination .page-item.next:not(.disabled)');

        $href = $I->grabAttributeFrom('.pagination .page-item.next a', 'href');
        $I->executeInSelenium(static function (RemoteWebDriver $webDriver) use ($href) {
            $webDriver->executeScript('window.location.href = "' . $href . '"');
        });

        // On page two the "2" item is the active one.
        $I->waitForElement('.pagination .page-item.active', 10);
        $I->see('2', '.pagination .page-item.active');
    }
}
