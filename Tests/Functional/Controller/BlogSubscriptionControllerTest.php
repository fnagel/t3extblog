<?php

declare(strict_types=1);

namespace FelixNagel\T3extblog\Tests\Functional\Controller;

/*
 * This file is part of the "t3extblog" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use GuzzleHttp\Psr7\Utils;
use PHPUnit\Framework\Attributes\Test;
use TYPO3\CMS\Core\Cache\CacheManager;
use TYPO3\TestingFramework\Core\Functional\Framework\Frontend\InternalRequest;

final class BlogSubscriptionControllerTest extends AbstractControllerTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->importCSVDataSet(__DIR__ . '/../Fixtures/pages.csv');

        $this->setUpFrontendWithTypoScript(1, 'T3extblog', 'BlogSubscription');
    }

    #[Test]
    public function newActionReturns200(): void
    {
        $response = $this->executeFrontendSubRequest(
            (new InternalRequest())
                ->withQueryParameter('tx_t3extblog_blogsubscription[controller]', 'BlogSubscriberForm')
                ->withQueryParameter('tx_t3extblog_blogsubscription[action]', 'new')
        );

        self::assertSame(200, $response->getStatusCode());
    }

    #[Test]
    public function createActionWithValidDataRedirectsToSuccessAction(): void
    {
        $this->addTypoScriptToTemplateRecord(
            1,
            'plugin.tx_t3extblog.settings.blogSubscription.spamCheck.enable = 0'
        );

        $response = $this->executeFrontendSubRequest($this->buildCreateRequest('new@example.com'));

        self::assertSame(303, $response->getStatusCode());
        self::assertStringContainsString(
            'tx_t3extblog_blogsubscription%5Baction%5D=success',
            $response->getHeaderLine('Location')
        );
        self::assertSame(1, $this->countSubscribersByEmail('new@example.com'));
    }

    #[Test]
    public function successfulSubscriptionsCountAgainstRateLimit(): void
    {
        $this->addTypoScriptToTemplateRecord(
            1,
            'plugin.tx_t3extblog.settings.blogSubscription.rateLimit.enable = 1'
            . "\n" . 'plugin.tx_t3extblog.settings.blogSubscription.rateLimit.limit = 2'
            . "\n" . 'plugin.tx_t3extblog.settings.blogSubscription.spamCheck.enable = 0'
        );

        // The rate limiter state is stored in a file cache which is shared by all tests of this class
        $this->get(CacheManager::class)->getCache('ratelimiter')->flush();

        // First two subscriptions are created successfully
        $response1 = $this->executeFrontendSubRequest($this->buildCreateRequest('first@example.com'));
        self::assertSame(303, $response1->getStatusCode());
        self::assertSame(1, $this->countSubscribersByEmail('first@example.com'));

        $response2 = $this->executeFrontendSubRequest($this->buildCreateRequest('second@example.com'));
        self::assertSame(303, $response2->getStatusCode());
        self::assertSame(1, $this->countSubscribersByEmail('second@example.com'));

        // Third subscription must be rate limited, the limiter must not be reset after a successful request
        $response3 = $this->executeFrontendSubRequest($this->buildCreateRequest('third@example.com'));
        self::assertNotSame(303, $response3->getStatusCode());
        self::assertSame(0, $this->countSubscribersByEmail('third@example.com'));
    }

    protected function buildCreateRequest(string $email): InternalRequest
    {
        $ns = 'tx_t3extblog_blogsubscription';
        $trustedProperties = $this->buildTrustedPropertiesToken($ns, [
            "{$ns}[subscriber][email]",
            "{$ns}[subscriber][privacyPolicyAccepted]",
        ]);

        return (new InternalRequest())
            ->withMethod('POST')
            ->withQueryParameter($ns . '[controller]', 'BlogSubscriberForm')
            ->withQueryParameter($ns . '[action]', 'create')
            ->withBody(Utils::streamFor(http_build_query([
                $ns => [
                    '__trustedProperties' => $trustedProperties,
                    'subscriber' => [
                        'email' => $email,
                        'privacyPolicyAccepted' => '1',
                    ],
                ],
            ])));
    }

    protected function countSubscribersByEmail(string $email): int
    {
        // New subscribers are hidden until confirmed, so do not use the default restrictions
        $queryBuilder = $this->getConnectionPool()->getQueryBuilderForTable('tx_t3blog_blog_nl');
        $queryBuilder->getRestrictions()->removeAll();

        return (int)$queryBuilder
            ->count('uid')
            ->from('tx_t3blog_blog_nl')
            ->where($queryBuilder->expr()->eq('email', $queryBuilder->createNamedParameter($email)))
            ->executeQuery()
            ->fetchOne();
    }

    #[Test]
    public function createActionWithoutSubscriberRedirectsToNewAction(): void
    {
        $response = $this->executeFrontendSubRequest(
            (new InternalRequest())
                ->withMethod('POST')
                ->withQueryParameter('tx_t3extblog_blogsubscription[controller]', 'BlogSubscriberForm')
                ->withQueryParameter('tx_t3extblog_blogsubscription[action]', 'create')
        );

        self::assertSame(303, $response->getStatusCode());
    }
}
