<?php

declare(strict_types=1);

namespace FelixNagel\T3extblog\Tests\Functional\Controller;

/*
 * This file is part of the "t3extblog" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use PHPUnit\Framework\Attributes\Test;
use TYPO3\TestingFramework\Core\Functional\Framework\Frontend\InternalRequest;
use TYPO3\TestingFramework\Core\Functional\Framework\Frontend\InternalRequestContext;

/**
 * Tests the comment form prefilling feature (settings.blogsystem.comments.prefillFields).
 *
 * When enabled, the comment form email and author fields are pre-filled from
 * the logged-in FE user session or from a blog subscription authentication.
 */
final class CommentPrefillTest extends AbstractControllerTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->importCSVDataSet(__DIR__ . '/../Fixtures/pages.csv');
        $this->importCSVDataSet(__DIR__ . '/../Fixtures/posts.csv');
        $this->importCSVDataSet(__DIR__ . '/../Fixtures/comments.csv');
        $this->importCSVDataSet(__DIR__ . '/../Fixtures/categories.csv');
        $this->importCSVDataSet(__DIR__ . '/../Fixtures/be_users.csv');
        $this->importCSVDataSet(__DIR__ . '/../Fixtures/fe_groups.csv');
        $this->importCSVDataSet(__DIR__ . '/../Fixtures/fe_users.csv');

        $this->setUpFrontendWithTypoScript();
    }

    #[Test]
    public function commentFormDoesNotPrefillWhenDisabled(): void
    {
        $this->addTypoScriptToTemplateRecord(
            1,
            'plugin.tx_t3extblog.settings.blogsystem.comments.prefillFields.enable = 0'
        );

        $response = $this->executeFrontendSubRequest(
            (new InternalRequest())
                ->withQueryParameter('tx_t3extblog_blogsystem[controller]', 'Post')
                ->withQueryParameter('tx_t3extblog_blogsystem[action]', 'show')
                ->withQueryParameter('tx_t3extblog_blogsystem[post]', '1'),
            (new InternalRequestContext())->withFrontendUserId(1)
        );

        $body = (string)$response->getBody();

        // The form should not contain the FE user's email prefilled
        self::assertStringNotContainsString('testuser@example.com', $body);
    }

    #[Test]
    public function commentFormPrefillsEmailForLoggedInUser(): void
    {
        // Enable prefill fields
        $this->addTypoScriptToTemplateRecord(
            1,
            'plugin.tx_t3extblog.settings.blogsystem.comments.prefillFields.enable = 1'
            . "\n" . 'plugin.tx_t3extblog.settings.blogsystem.comments.prefillFields.authorField = fullName'
        );

        $response = $this->executeFrontendSubRequest(
            (new InternalRequest())
                ->withQueryParameter('tx_t3extblog_blogsystem[controller]', 'Post')
                ->withQueryParameter('tx_t3extblog_blogsystem[action]', 'show')
                ->withQueryParameter('tx_t3extblog_blogsystem[post]', '1'),
            (new InternalRequestContext())->withFrontendUserId(1)
        );

        self::assertSame(200, $response->getStatusCode());
        $body = (string)$response->getBody();

        // The FE user's email should be pre-filled in the form
        self::assertStringContainsString('testuser@example.com', $body);
    }

    #[Test]
    public function commentFormPrefillsAuthorFullNameForLoggedInUser(): void
    {
        // Enable prefill fields with fullName
        $this->addTypoScriptToTemplateRecord(
            1,
            'plugin.tx_t3extblog.settings.blogsystem.comments.prefillFields.enable = 1'
            . "\n" . 'plugin.tx_t3extblog.settings.blogsystem.comments.prefillFields.authorField = fullName'
        );

        $response = $this->executeFrontendSubRequest(
            (new InternalRequest())
                ->withQueryParameter('tx_t3extblog_blogsystem[controller]', 'Post')
                ->withQueryParameter('tx_t3extblog_blogsystem[action]', 'show')
                ->withQueryParameter('tx_t3extblog_blogsystem[post]', '1'),
            (new InternalRequestContext())->withFrontendUserId(1)
        );

        self::assertSame(200, $response->getStatusCode());
        $body = (string)$response->getBody();

        // The author field should contain "Max Mustermann" (first_name + last_name)
        self::assertStringContainsString('Max Mustermann', $body);
    }

    #[Test]
    public function commentFormPrefillsAuthorFromUsernameField(): void
    {
        // Enable prefill with "username" field instead of fullName
        $this->addTypoScriptToTemplateRecord(
            1,
            'plugin.tx_t3extblog.settings.blogsystem.comments.prefillFields.enable = 1'
            . "\n" . 'plugin.tx_t3extblog.settings.blogsystem.comments.prefillFields.authorField = username'
        );

        $response = $this->executeFrontendSubRequest(
            (new InternalRequest())
                ->withQueryParameter('tx_t3extblog_blogsystem[controller]', 'Post')
                ->withQueryParameter('tx_t3extblog_blogsystem[action]', 'show')
                ->withQueryParameter('tx_t3extblog_blogsystem[post]', '1'),
            (new InternalRequestContext())->withFrontendUserId(1)
        );

        self::assertSame(200, $response->getStatusCode());
        $body = (string)$response->getBody();

        // The author field should contain the username
        self::assertStringContainsString('testuser', $body);
    }
}
