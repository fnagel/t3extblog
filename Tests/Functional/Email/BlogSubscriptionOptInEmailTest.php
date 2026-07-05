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
 * Tests the double-opt-in email sent by BlogNotificationService when a visitor
 * subscribes to new blog posts through the frontend subscription form.
 *
 * This exercises BlogNotificationService::processNewEntity() -> sendOptInMail()
 * and, together with CommentNotificationEmailTest, covers both notification
 * services' mail sending (recipient, sender address) via file spooling.
 */
final class BlogSubscriptionOptInEmailTest extends AbstractEmailTestCase
{
    protected const PLUGIN = 'tx_t3extblog_blogsubscription';

    protected const FROM_EMAIL = 'blog@example.com';

    protected function setUp(): void
    {
        parent::setUp();

        $this->importCSVDataSet(__DIR__ . '/../Fixtures/pages.csv');

        $this->setUpFrontendWithTypoScript(1, 'T3extblog', 'BlogSubscription');
        $this->addTypoScriptToTemplateRecord(
            1,
            implode("\n", [
                // The opt-in mail links back to the subscription/blog pages.
                'plugin.tx_t3extblog.settings.blogsystem.pid = 1',
                'plugin.tx_t3extblog.settings.subscriptionManager.pid = 1',
                'plugin.tx_t3extblog.settings.subscriptionManager.blog.subscriber.mailFrom.email = ' . self::FROM_EMAIL,
                // Keep the spam check out of the way for a clean subscription.
                'plugin.tx_t3extblog.settings.blogSubscription.spamCheck.enable = 0',
            ])
        );
    }

    #[Test]
    public function subscribingToBlogSendsOptInMailToSubscriber(): void
    {
        $response = $this->subscribe('opt-in@example.com');

        // A successful subscription redirects to the success action.
        self::assertSame(303, $response->getStatusCode());

        $mail = $this->findEmailTo('opt-in@example.com');
        self::assertInstanceOf(Email::class, $mail, 'No opt-in mail was sent to the new blog subscriber.');
        self::assertSame(self::FROM_EMAIL, $mail->getFrom()[0]->getAddress());
    }

    #[Test]
    public function optInMailHasExactlyOneRecipient(): void
    {
        $this->subscribe('single@example.com');

        $mail = $this->findEmailTo('single@example.com');
        self::assertInstanceOf(Email::class, $mail);
        self::assertCount(1, $mail->getTo());
        self::assertNotSame($mail->getTo()[0]->getAddress(), $mail->getFrom()[0]->getAddress());
    }

    protected function subscribe(string $email): \Psr\Http\Message\ResponseInterface
    {
        $trustedProperties = $this->buildTrustedPropertiesToken(self::PLUGIN, [
            self::PLUGIN . '[subscriber][email]',
            self::PLUGIN . '[subscriber][privacyPolicyAccepted]',
        ]);

        return $this->executeFrontendSubRequest(
            (new InternalRequest())
                ->withMethod('POST')
                ->withQueryParameter(self::PLUGIN . '[controller]', 'BlogSubscriberForm')
                ->withQueryParameter(self::PLUGIN . '[action]', 'create')
                ->withBody(Utils::streamFor(http_build_query([
                    self::PLUGIN => [
                        '__trustedProperties' => $trustedProperties,
                        'subscriber' => [
                            'email' => $email,
                            'privacyPolicyAccepted' => '1',
                        ],
                    ],
                ])))
        );
    }
}
