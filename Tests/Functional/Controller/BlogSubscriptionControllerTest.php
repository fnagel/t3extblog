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
        $response = $this->executeFrontendSubRequest(
            (new InternalRequest())
                ->withMethod('POST')
                ->withQueryParameter('tx_t3extblog_blogsubscription[controller]', 'BlogSubscriberForm')
                ->withQueryParameter('tx_t3extblog_blogsubscription[action]', 'create')
                ->withParsedBody([
                    'tx_t3extblog_blogsubscription[subscriber][email]' => 'new@example.com',
                    'tx_t3extblog_blogsubscription[subscriber][privacyPolicyAccepted]' => '1',
                ])
        );

        self::assertSame(303, $response->getStatusCode());
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
