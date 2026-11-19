<?php

declare(strict_types=1);

namespace FelixNagel\T3extblog\Tests\Functional\ViewHelpers\Backend\Link;

/*
 * This file is part of the "t3extblog" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use FelixNagel\T3extblog\Tests\Functional\AbstractFunctionalTestCase;
use PHPUnit\Framework\Attributes\Test;
use TYPO3\CMS\Core\Core\SystemEnvironmentBuilder;
use TYPO3\CMS\Core\Http\NormalizedParams;
use TYPO3\CMS\Core\Http\ServerRequest;
use TYPO3\CMS\Fluid\Core\Rendering\RenderingContextFactory;
use TYPO3Fluid\Fluid\View\TemplateView;

/**
 * The view helper creates the approve / spam toggle links of the backend comment module.
 * Request values must never end up unencoded in the DataHandler URL (parameter injection).
 */
final class IssueCommandViewHelperTest extends AbstractFunctionalTestCase
{
    private const INJECTED_PAGE_ID = '1&data[be_users][1][admin]=1&cmd[pages][1][delete]=1';

    protected function setUp(): void
    {
        parent::setUp();

        $this->importCSVDataSet(__DIR__ . '/../../../Fixtures/be_users.csv');
        $this->setUpBackendUser(1);
    }

    #[Test]
    public function linkContainsOnlyGivenDataHandlerParameters(): void
    {
        $query = $this->renderLinkAndGetQuery(
            'parameters="data[tx_t3blog_com][5][approved]=1" redirectUrl="/typo3/module/web/T3extblogBlogsystem"'
        );

        self::assertSame(['tx_t3blog_com' => ['5' => ['approved' => '1']]], $query['data']);
        self::assertArrayNotHasKey('cmd', $query);
        // Needed for the TypoScript settings in the DataHandler hook, only the integer value is used
        self::assertSame('1', $query['id']);
        self::assertSame('/typo3/module/web/T3extblogBlogsystem', $query['redirect']);
        self::assertNotEmpty($query['token']);
    }

    #[Test]
    public function requestUriIsUsedAsEncodedRedirectUrl(): void
    {
        $query = $this->renderLinkAndGetQuery('parameters="data[tx_t3blog_com][5][spam]=0"');

        self::assertSame(['tx_t3blog_com' => ['5' => ['spam' => '0']]], $query['data']);
        self::assertArrayNotHasKey('cmd', $query);
        // The injected parameters are only part of the (encoded) redirect URL
        self::assertStringStartsWith('/typo3/module/web/T3extblogBlogsystem?id=', $query['redirect']);
    }

    /**
     * Renders the view helper in a backend request with a manipulated page id and returns the parsed link query.
     */
    private function renderLinkAndGetQuery(string $arguments): array
    {
        $request = $this->createBackendRequest();

        $renderingContext = $this->get(RenderingContextFactory::class)->create([], $request);
        $renderingContext->getTemplatePaths()->setTemplateSource(
            '{namespace t3b=FelixNagel\T3extblog\ViewHelpers\Backend}'
            . '<t3b:link.issueCommand ' . $arguments . '>Link</t3b:link.issueCommand>'
        );
        $html = (string)(new TemplateView($renderingContext))->render();

        self::assertSame(1, preg_match('/href="([^"]+)"/', $html, $matches), 'No link rendered: ' . $html);
        $url = html_entity_decode($matches[1]);

        self::assertStringEndsWith('/record/commit', (string)parse_url($url, PHP_URL_PATH));
        parse_str((string)parse_url($url, PHP_URL_QUERY), $query);

        return $query;
    }

    private function createBackendRequest(): ServerRequest
    {
        $uri = 'https://localhost/typo3/module/web/T3extblogBlogsystem?id=' . rawurlencode(self::INJECTED_PAGE_ID);
        $request = (new ServerRequest($uri, 'GET', null, [], [
            'HTTP_HOST' => 'localhost',
            'HTTPS' => 'on',
            'REQUEST_URI' => '/typo3/module/web/T3extblogBlogsystem?id=' . rawurlencode(self::INJECTED_PAGE_ID),
            'SCRIPT_NAME' => '/typo3/index.php',
        ]))
            ->withQueryParams(['id' => self::INJECTED_PAGE_ID])
            ->withAttribute('applicationType', SystemEnvironmentBuilder::REQUESTTYPE_BE);
        $request = $request->withAttribute('normalizedParams', NormalizedParams::createFromRequest($request));

        $GLOBALS['TYPO3_REQUEST'] = $request;

        return $request;
    }
}
