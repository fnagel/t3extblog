<?php

declare(strict_types=1);

namespace FelixNagel\T3extblog\Middleware;

/**
 * This file is part of the "t3extblog" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use TYPO3\CMS\Core\PageTitle\PageTitleProviderManager;
use TYPO3\CMS\Core\PageTitle\RecordTitleProvider;

/**
 * Reset the page title set by this extension (record title provider) at the beginning of each frontend request
 * rendered within a CLI process.
 *
 * Title providers are singletons. When rendering multiple frontend requests in one process (e.g. frontend indexing
 * of EXT:index within a message worker) the title of the previous request would be reused and extended.
 * Regular frontend requests (one request per process) are not affected.
 *
 * Only registered when EXT:index is installed, see Configuration/RequestMiddlewares.php.
 */
class ResetPageTitleMiddleware implements MiddlewareInterface
{
    public function __construct(
        protected RecordTitleProvider $recordTitleProvider,
        protected PageTitleProviderManager $pageTitleProviderManager,
    ) {
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        // Only needed for frontend requests within a CLI process, like frontend indexing (EXT:index)
        // Not using Environment::isCli() as it's changed by EXT:index
        if (PHP_SAPI === 'cli') {
            $this->recordTitleProvider->setTitle('');
            $this->pageTitleProviderManager->setPageTitleCache([]);
        }

        return $handler->handle($request);
    }
}
