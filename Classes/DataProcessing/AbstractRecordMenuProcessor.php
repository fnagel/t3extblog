<?php

namespace FelixNagel\T3extblog\DataProcessing;

/**
 * This file is part of the "t3extblog" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Persistence\RepositoryInterface;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use TYPO3\CMS\Frontend\ContentObject\DataProcessorInterface;

abstract class AbstractRecordMenuProcessor implements DataProcessorInterface
{
    abstract protected function getRepository(array $configuration): RepositoryInterface;

    abstract protected function getUid(array $configuration, ContentObjectRenderer $cObj): ?int;

    abstract protected function getRecord(array $configuration, int $uid): ?array;

    protected function getTitle(array $record): string
    {
        return $record['title'];
    }

    /**
     * @SuppressWarnings("PHPMD.UnusedFormalParameter")
     */
    public function process(
        ContentObjectRenderer $cObj,
        array $contentObjectConfiguration,
        array $processorConfiguration,
        array $processedData
    ): array {
        if (!$processorConfiguration['as']) {
            return $processedData;
        }

        if (($uid = $this->getUid($processorConfiguration, $cObj)) === null) {
            return $processedData;
        }

        $record = $this->getRecord($processorConfiguration, $uid);

        if ($record) {
            foreach (GeneralUtility::trimExplode(',', $processorConfiguration['as'], true) as $menu) {
                if (isset($processedData[$menu])) {
                    $this->addRecordToMenu($record, $processedData[$menu]);
                }
            }
        }

        return $processedData;
    }

    protected function addRecordToMenu(array $record, array &$menu): void
    {
        foreach ($menu as &$menuItem) {
            $menuItem['current'] = 0;
        }

        $menu[] = [
            'data' => $record,
            'title' => $this->getTitle($record),
            'active' => 1,
            'current' => 1,
            'link' => GeneralUtility::getIndpEnv('TYPO3_SITE_SCRIPT'),
        ];
    }
}
