<?php

declare(strict_types=1);

namespace FelixNagel\T3extblog\Index\Indexing;

use Lochmueller\Index\Domain\Repository\GenericRepository;
use Lochmueller\Index\Indexing\Database\ContentIndexing;
use Lochmueller\Index\Indexing\Database\ContentType\ContentTypeInterface;
use Lochmueller\Index\Indexing\Database\ContentType\HeaderContentType;
use Lochmueller\Index\Indexing\Database\DatabaseIndexingDto;
use Lochmueller\Index\Traversing\RecordSelection;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use TYPO3\CMS\Core\Core\SystemEnvironmentBuilder;
use TYPO3\CMS\Core\Domain\Record;
use TYPO3\CMS\Core\Http\ServerRequest;
use TYPO3\CMS\Core\Site\Entity\Site;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;

/**
 * Content type for the database indexing of EXT:index.
 *
 * Creates a variant for each post of the blogsystem plugin. The post storage is taken from the
 * TypoScript of the plugin page.
 */
#[AutoconfigureTag(name: 'index.content_type')]
class PostContentType implements ContentTypeInterface
{
    /**
     * @var array<int, int[]> Storage PIDs indexed by plugin page UID
     */
    protected array $storagePids = [];

    public function __construct(
        protected HeaderContentType $headerContentType,
        protected RecordSelection   $recordSelection,
        protected ContentIndexing   $contentIndexing,
        protected GenericRepository $genericRepository,
        protected ConfigurationManagerInterface $configurationManager,
    ) {
    }

    public function canHandle(Record $record): bool
    {
        return $record->getRecordType() === 't3extblog_blogsystem';
    }

    public function addContent(Record $record, DatabaseIndexingDto $dto): void
    {
        $id = (int)($dto->arguments['tx_t3extblog_blogsystem']['post'] ?? 0);
        if ($id <= 0) {
            return;
        }

        $this->headerContentType->addContent($record, $dto);
        $table = 'tx_t3blog_post';
        $row = $this->genericRepository->setTableName($table)->findByUid($id);
        if ($row === null) {
            return;
        }
        $record = $this->recordSelection->mapRecord($table, $row);

        // @extensionScannerIgnoreLine
        $dto->title = $record->get('title') . ' | ' . $dto->site->getAttribute('websiteTitle');

        // @extensionScannerIgnoreLine
        $dto->content .= $record->get('title') . ' ';
        // @extensionScannerIgnoreLine
        $dto->content .= $record->get('preview_text') . ' ';

        foreach ($record->get('content') as $ce) {
            $this->contentIndexing->addContent($ce, $dto);
        }
    }

    /**
     * @param \SplQueue<DatabaseIndexingDto> $queue
     *
     * @SuppressWarnings("PHPMD.UnusedFormalParameter")
     */
    public function addVariants(Record $record, \SplQueue &$queue): void
    {
        /* @var DatabaseIndexingDto $dto */
        $dto = $queue->offsetGet(0);

        $variants = [];
        foreach ($this->getRecords($dto->site, $dto->pageUid, $dto->languageUid) as $post) {
            $arguments = [
                '_language' => $dto->languageUid,
                'tx_t3extblog_blogsystem' => [
                    'controller' => 'Post',
                    'action' => 'show',
                    'post' => $post->getUid(),
                ],
            ];

            $variants[] = new DatabaseIndexingDto(
                // @extensionScannerIgnoreLine
                $dto->title,
                // @extensionScannerIgnoreLine
                $dto->content,
                $dto->pageUid,
                $dto->languageUid,
                $arguments,
                $dto->site
            );
        }

        // Keep the original item (list view) if no posts are found, e.g. missing storage configuration
        if ($variants === []) {
            return;
        }

        while (!$queue->isEmpty()) {
            $queue->dequeue();
        }
        foreach ($variants as $variant) {
            $queue->enqueue($variant);
        }
    }

    /**
     * @return iterable<Record>
     */
    protected function getRecords(Site $site, int $pageUid, int $languageUid): iterable
    {
        yield from $this->recordSelection->findRecordsOnPage(
            'tx_t3blog_post',
            $this->getStoragePids($site, $pageUid),
            $languageUid
        );
    }

    /**
     * Get storage PIDs from TypoScript (plugin.tx_t3extblog.persistence.storagePid) of the plugin page.
     *
     * Indexing usually runs in CLI context without request, so we need to create a backend
     * request for the plugin page. The shared configuration manager is cloned to not change
     * the request of other code running in the same process.
     *
     * @return int[]
     */
    protected function getStoragePids(Site $site, int $pageUid): array
    {
        if (!isset($this->storagePids[$pageUid])) {
            $request = (new ServerRequest())
                ->withAttribute('applicationType', SystemEnvironmentBuilder::REQUESTTYPE_BE)
                ->withAttribute('site', $site)
                ->withQueryParams(['id' => $pageUid]);

            $configurationManager = clone $this->configurationManager;
            $configurationManager->setRequest($request);
            $configuration = $configurationManager->getConfiguration(
                ConfigurationManagerInterface::CONFIGURATION_TYPE_FRAMEWORK,
                'T3extblog'
            );

            $this->storagePids[$pageUid] = GeneralUtility::intExplode(
                ',',
                (string)($configuration['persistence']['storagePid'] ?? ''),
                true
            );
        }

        return $this->storagePids[$pageUid];
    }
}
