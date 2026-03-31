<?php

declare(strict_types=1);

namespace FelixNagel\T3extblog\Tests\Acceptance\Frontend;

/*
 * This file is part of the "t3extblog" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use Codeception\Attribute\Depends;
use FelixNagel\T3extblog\Tests\Acceptance\Support\AcceptanceTester;

/**
 * Frontend acceptance tests for the blog system plugin.
 *
 * Covers the full public-facing blog workflow following the
 * "Quick test procedure" from Documentation/DeveloperGuide/Index.rst:244:
 * - Post list and detail views
 * - Comment form rendering, submission and subscription
 * - Visibility of approved vs spam/unapproved comments
 * - Admin approval simulation via direct DB update
 *
 * Uses Codeception #[Depends] to enforce execution order and the Db module
 * to verify database state and simulate backend actions.
 */
final class BlogFrontendCest
{
    /**
     * Step 1: The post list shows published posts but not hidden (draft) ones.
     */
    public function postListShowsPublishedPosts(AcceptanceTester $I): void
    {
        $I->amOnPage('/');
        $I->see('First Post');
        $I->see('Second Post');
        $I->dontSee('Draft Post');
    }

    /**
     * Step 2: Clicking a post title navigates to the post detail page.
     */
    #[Depends('postListShowsPublishedPosts')]
    public function postDetailShowsContent(AcceptanceTester $I): void
    {
        $I->amOnPage('/');
        $I->click('First Post');
        $I->see('First Post', 'h1');
        $I->seeInTitle('First Post');
        $I->seeInCurrentUrl('first-post');
    }

    /**
     * Step 3: The comment form is rendered on the post detail page.
     */
    #[Depends('postDetailShowsContent')]
    public function commentFormRendersOnDetailPage(AcceptanceTester $I): void
    {
        $I->amOnPage('/');
        $I->click('First Post');
        $I->seeElement('form');
        $I->seeElement('input[name*="[author]"]');
        $I->seeElement('input[name*="[email]"]');
        $I->seeElement('textarea[name*="[text]"]');
    }

    /**
     * Step 4: A valid comment can be submitted successfully.
     */
    #[Depends('commentFormRendersOnDetailPage')]
    public function addCommentSuccessfully(AcceptanceTester $I): void
    {
        $I->amOnPage('/');
        $I->click('First Post');

        $I->fillField('input[name*="[author]"]', 'Test User');
        $I->fillField('input[name*="[email]"]', 'testuser@example.com');
        $I->fillField('textarea[name*="[text]"]', 'This is a test comment from an acceptance test.');
        $I->checkOption('input[type="checkbox"][name*="[privacyPolicyAccepted]"]');
        $I->checkOption('input[type="checkbox"][name*="[human]"]');
        $I->click('button.btn-primary');

        $I->seeInCurrentUrl('first-post');

        // Verify the comment was persisted in the database
        $I->seeInDatabase('tx_t3blog_com', [
            'fk_post' => 1,
            'hidden' => 0,
            'approved' => 0,
            'author' => 'Test User',
            'email' => 'testuser@example.com',
        ]);
    }

    /**
     * Step 5: Submit a comment with subscription checkbox.
     * Verify the subscription record is created in the database.
     */
    #[Depends('addCommentSuccessfully')]
    public function addCommentWithSubscription(AcceptanceTester $I): void
    {
        $I->amOnPage('/');
        $I->click('First Post');

        $I->fillField('input[name*="[author]"]', 'Subscriber User');
        $I->fillField('input[name*="[email]"]', 'subscriber@example.com');
        $I->fillField('textarea[name*="[text]"]', 'I want to subscribe to new comments.');
        $I->checkOption('input[type="checkbox"][name*="[privacyPolicyAccepted]"]');
        $I->checkOption('input[type="checkbox"][name*="[human]"]');
        $I->checkOption('input[type="checkbox"][name*="[subscribe]"]');

        $I->click('button.btn-primary');

        $I->seeInCurrentUrl('first-post');

        // Verify the comment was saved
        $I->seeInDatabase('tx_t3blog_com', [
            'fk_post' => 1,
            'hidden' => 0,
            'approved' => 0,
            'author' => 'Subscriber User',
            'email' => 'subscriber@example.com',
        ]);

        // The subscription record should exist in the database
        $I->seeInDatabase('tx_t3blog_com_nl', [
            'post_uid' => 1,
            'name' => 'Subscriber User',
            'email' => 'subscriber@example.com',
            'hidden' => 1,
            'lastsent' => 0,
        ]);
    }

    /**
     * Step 6: The post list still renders correctly after comment submissions.
     */
    #[Depends('addCommentWithSubscription')]
    public function postListStillRendersCorrectly(AcceptanceTester $I): void
    {
        $I->amOnPage('/');
        $I->see('First Post');
        $I->see('Second Post');
        $I->dontSee('Draft Post');
    }

    /**
     * Step 7: Only approved comments are visible — unapproved/spam ones are hidden.
     * Alice's comment (approved=1 in fixtures) should be visible.
     * Newly submitted comments (unapproved) should not be visible.
     */
    #[Depends('postListStillRendersCorrectly')]
    public function approvedCommentsAreVisibleNewOnesAreNot(AcceptanceTester $I): void
    {
        $I->amOnPage('/');
        $I->click('First Post');

        // Alice's approved comment from fixtures should be visible
        $I->see('Alice');
        $I->see('Great post!');

        // Newly submitted comments are unapproved — should NOT be visible
        $I->dontSee('Test User');
        $I->dontSee('Subscriber User');
    }

    /**
     * Step 8: Simulate an admin approving a comment via direct DB update.
     * After approval the comment must become visible on the frontend.
     */
    #[Depends('approvedCommentsAreVisibleNewOnesAreNot')]
    public function adminApprovesCommentAndItBecomesVisible(AcceptanceTester $I): void
    {
        // Grab the UID of the "Test User" comment
        $commentUid = $I->grabFromDatabase('tx_t3blog_com', 'uid', [
            'author' => 'Test User',
            'email' => 'testuser@example.com',
        ]);

        // Simulate admin approval: set approved=1 and spam=0
        $I->updateInDatabase('tx_t3blog_com', [
            'approved' => 1,
            'spam' => 0,
        ], ['uid' => $commentUid]);

        // Reload the post detail page — the approved comment should now be visible
        $I->amOnPage('/');
        $I->click('First Post');
        $I->see('Test User');
        $I->see('This is a test comment from an acceptance test.');

        // The subscription comment is still unapproved
        $I->dontSee('Subscriber User');
    }
}
