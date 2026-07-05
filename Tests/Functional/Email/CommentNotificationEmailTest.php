<?php

declare(strict_types=1);

namespace FelixNagel\T3extblog\Tests\Functional\Email;

/*
 * This file is part of the "t3extblog" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use GuzzleHttp\Psr7\Utils;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Component\Mime\Email;
use TYPO3\TestingFramework\Core\Functional\Framework\Frontend\InternalRequest;

/**
 * Tests emails sent when a new comment is submitted via the frontend.
 *
 * A real comment is created (spam check disabled, comments auto-approved) which
 * triggers CommentNotificationService::notifyAdmin() and processNewEntity().
 * The resulting mails are captured through file spooling and asserted.
 */
class CommentNotificationEmailTest extends AbstractEmailTestCase
{
    protected const ADMIN_EMAIL = 'admin@example.com';

    protected const FROM_EMAIL = 'blog@example.com';

    protected function setUp(): void
    {
        parent::setUp();

        $this->importCSVDataSet(__DIR__ . '/../Fixtures/pages.csv');
        $this->importCSVDataSet(__DIR__ . '/../Fixtures/posts.csv');

        $this->setUpFrontendWithTypoScript();

        // Make comment creation actually succeed and configure mail addresses.
        $this->addTypoScriptToTemplateRecord(
            1,
            implode("\n", array_merge([
                'plugin.tx_t3extblog.settings.blogsystem.comments.spamCheck.enable = 0',
                'plugin.tx_t3extblog.settings.blogsystem.comments.approvedByDefault = 1',
                'plugin.tx_t3extblog.settings.blogsystem.comments.allowedUntil =',
                'plugin.tx_t3extblog.settings.subscriptionManager.comment.admin.mailTo.email = ' . self::ADMIN_EMAIL,
                'plugin.tx_t3extblog.settings.subscriptionManager.comment.admin.mailFrom.email = ' . self::FROM_EMAIL,
                'plugin.tx_t3extblog.settings.subscriptionManager.comment.subscriber.mailFrom.email = ' . self::FROM_EMAIL,
            ], $this->additionalTypoScript()))
        );
    }

    /**
     * Extra TypoScript setup lines for subclasses (e.g. to switch the email type).
     *
     * @return string[]
     */
    protected function additionalTypoScript(): array
    {
        return [];
    }

    #[Test]
    public function adminIsNotifiedWhenNewCommentIsSubmitted(): void
    {
        $this->submitComment([
            'author' => 'Commenter',
            'email' => 'commenter@example.com',
            'text' => 'A perfectly reasonable comment.',
            'privacyPolicyAccepted' => '1',
        ]);

        $adminMail = $this->findEmailTo(self::ADMIN_EMAIL);
        self::assertInstanceOf(Email::class, $adminMail, 'No notification mail was sent to the admin.');
    }

    #[Test]
    public function subscriberReceivesOptInMailWhenSubscribing(): void
    {
        $subscriberEmail = 'subscriber@example.com';

        $this->submitComment([
            'author' => 'New Subscriber',
            'email' => $subscriberEmail,
            'text' => 'Please keep me posted.',
            'privacyPolicyAccepted' => '1',
            'subscribe' => '1',
        ]);

        $optInMail = $this->findEmailTo($subscriberEmail);
        self::assertInstanceOf(Email::class, $optInMail, 'No opt-in mail was sent to the subscribing user.');
        self::assertSame(self::FROM_EMAIL, $optInMail->getFrom()[0]->getAddress());
    }

    /**
     * Regression test for the "from"/"to" address mix-up.
     *
     * @see https://github.com/fnagel/t3extblog/commit/3b25810814cdb2fb0d0d4b1bfd2973dd7fb62fc4
     */
    #[Test]
    public function adminNotificationHasCorrectToAndFromAddresses(): void
    {
        $this->submitComment([
            'author' => 'Commenter',
            'email' => 'commenter@example.com',
            'text' => 'Checking the envelope addresses.',
            'privacyPolicyAccepted' => '1',
        ]);

        $adminMail = $this->findEmailTo(self::ADMIN_EMAIL);
        self::assertInstanceOf(Email::class, $adminMail, 'No notification mail was sent to the admin.');

        self::assertCount(1, $adminMail->getTo());
        self::assertSame(self::ADMIN_EMAIL, $adminMail->getTo()[0]->getAddress());

        self::assertCount(1, $adminMail->getFrom());
        self::assertSame(self::FROM_EMAIL, $adminMail->getFrom()[0]->getAddress());

        // The bug swapped the two: guard against them ever being equal again.
        self::assertNotSame(
            $adminMail->getTo()[0]->getAddress(),
            $adminMail->getFrom()[0]->getAddress()
        );
    }

    /**
     * Submit a comment for post 1 through the frontend Comment plugin.
     *
     * The full set of form fields is always sent — just like a real browser
     * submitting Partials/Comment/FormFields.html, where the optional "title"
     * and "website" inputs are posted as empty strings.
     *
     * @param array<string, string> $comment newComment field values (merged over the defaults)
     */
    protected function submitComment(array $comment): void
    {
        $comment = array_merge([
            'title' => '',
            'author' => '',
            'email' => '',
            'website' => '',
            'text' => '',
            'subscribe' => '0',
            'privacyPolicyAccepted' => '0',
        ], $comment);

        $ns = 'tx_t3extblog_blogsystem';
        $trustedProperties = $this->buildTrustedPropertiesToken($ns, array_merge(
            ["{$ns}[post]"],
            array_map(static fn (string $field): string => "{$ns}[newComment][{$field}]", array_keys($comment))
        ));

        $response = $this->executeFrontendSubRequest(
            (new InternalRequest())
                ->withMethod('POST')
                ->withQueryParameter('tx_t3extblog_blogsystem[controller]', 'Comment')
                ->withQueryParameter('tx_t3extblog_blogsystem[action]', 'create')
                ->withBody(Utils::streamFor(http_build_query([
                    'tx_t3extblog_blogsystem' => [
                        '__trustedProperties' => $trustedProperties,
                        'post' => '1',
                        'newComment' => $comment,
                    ],
                ])))
        );

        // The create action redirects to the post's single view on success.
        self::assertSame(303, $response->getStatusCode(), 'Comment was not created (no redirect).');
    }
}
