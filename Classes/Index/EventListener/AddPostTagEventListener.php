<?php

declare(strict_types=1);

namespace FelixNagel\T3extblog\Index\EventListener;

use Lochmueller\Seal\Event\BeforeSaveDocumentEvent;
use Lochmueller\Seal\Event\Enum\IndexType;
use TYPO3\CMS\Core\Attribute\AsEventListener;
use TYPO3\CMS\Core\Http\ServerRequest;
use TYPO3\CMS\Core\Routing\PageArguments;
use TYPO3\CMS\Core\Routing\SiteMatcher;
use TYPO3\CMS\Core\Routing\SiteRouteResult;
use TYPO3\CMS\Core\Site\Entity\Site;

/**
 * Adds a tag to all post documents of EXT:seal.
 *
 * Posts are detected by routing the document URI, so this works for all index technologies.
 */
class AddPostTagEventListener
{
    public const TAG = 'BlogPost';

    public function __construct(
        protected SiteMatcher $siteMatcher,
    ) {
    }

    #[AsEventListener('t3extblog/seal-add-post-tag')]
    public function __invoke(BeforeSaveDocumentEvent $event): void
    {
        if ($event->indexType !== IndexType::Page || !$event->site instanceof Site) {
            return;
        }

        if (!$this->isPostUri($event->site, (string)($event->document['uri'] ?? ''))) {
            return;
        }

        $tags = (array)($event->document['tags'] ?? []);
        if (!in_array(self::TAG, $tags, true)) {
            $tags[] = self::TAG;
        }
        $event->document['tags'] = $tags;
    }

    protected function isPostUri(Site $site, string $uri): bool
    {
        try {
            $request = new ServerRequest($uri, 'GET');
            $routeResult = $this->siteMatcher->matchRequest($request);
            // No language for empty or unknown URIs, page routing would fail
            if (!$routeResult instanceof SiteRouteResult || $routeResult->getLanguage() === null) {
                return false;
            }
            $pageArguments = $site->getRouter()->matchRequest($request, $routeResult);
        } catch (\Exception) {
            return false;
        }

        return $pageArguments instanceof PageArguments &&
            (int)($pageArguments->getRouteArguments()['tx_t3extblog_blogsystem']['post'] ?? 0) > 0;
    }
}
