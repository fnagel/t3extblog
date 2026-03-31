<?php

declare(strict_types=1);

namespace FelixNagel\T3extblog\Tests\Acceptance\Backend;

/*
 * This file is part of the "t3extblog" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use Codeception\Attribute\Depends;
use FelixNagel\T3extblog\Tests\Acceptance\Support\BackendTester;

/**
 * Backend acceptance tests for the TYPO3 dashboard with t3extblog widgets.
 *
 * Covers:
 * - Dashboard module accessibility
 * - Add-widget button presence
 * - Blog widget group availability in the widget selector
 * - Blog widgets render correctly when pre-configured via fixture
 */
final class DashboardCest
{
    public function _before(BackendTester $I): void
    {
        $I->useExistingSession('admin');
    }

    /**
     * The TYPO3 core dashboard module loads without errors and
     * the "Add widget" button is present.
     */
    public function dashboardModuleLoads(BackendTester $I): void
    {
        $I->click('Dashboard', '#modulemenu');
        $I->switchToContentFrame();
        $I->dontSee('Oops, an error occurred!');
        $I->dontSee('PHP Fatal error');
        $I->seeElement('.btn-dashboard-add-widget');
    }

    /**
     * Clicking the "Add widget" button opens a dialog that contains the
     * t3extblog widget group (displayed as "Blog") inside the shadow DOM.
     */
    #[Depends('dashboardModuleLoads')]
    public function blogWidgetGroupIsAvailable(BackendTester $I): void
    {
        $I->click('Dashboard', '#modulemenu');
        $I->switchToContentFrame();

        $I->waitForElementClickable('.btn-dashboard-add-widget', 10);
        $I->click('.btn-dashboard-add-widget');

        $I->switchToMainFrame();
        $I->waitForElementVisible('.modal', 10);

        // The widget groups are rendered inside the shadow DOM of typo3-backend-new-record-wizard.
        // Use JS to verify the "t3extblog" group navigation button exists.
        $I->waitForJS(
            'return document.querySelector(".modal typo3-backend-new-record-wizard")'
            . '?.shadowRoot?.querySelector("button[data-identifier=t3extblog]") !== null',
            10
        );

        // Close the modal
        $I->click('.modal .btn-default');
        $I->waitForElementNotVisible('.modal', 10);
    }

    /**
     * The "Blog statistic" widget (added via be_dashboards.csv fixture) renders
     * on the dashboard without errors.
     */
    #[Depends('blogWidgetGroupIsAvailable')]
    public function blogStatisticWidgetRendersCorrectly(BackendTester $I): void
    {
        $I->click('Dashboard', '#modulemenu');
        $I->switchToContentFrame();

        $I->waitForText('Blog statistic', 10);
        $I->see('Blog statistic');
        $I->dontSee('Oops, an error occurred!');
        $I->dontSee('PHP Fatal error');
    }

    /**
     * The "Pending blog comments" number widget (added via fixture) renders correctly.
     */
    #[Depends('blogStatisticWidgetRendersCorrectly')]
    public function pendingCommentsWidgetRendersCorrectly(BackendTester $I): void
    {
        $I->click('Dashboard', '#modulemenu');
        $I->switchToContentFrame();

        $I->waitForText('Pending blog comments', 10);
        $I->see('Pending blog comments');
        $I->dontSee('Oops, an error occurred!');
    }
}
