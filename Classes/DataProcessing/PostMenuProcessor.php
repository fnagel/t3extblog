<?php

namespace FelixNagel\T3extblog\DataProcessing;

/**
 * This file is part of the "t3extblog" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use FelixNagel\T3extblog\Controller\PostController;
use FelixNagel\T3extblog\Domain\Model\Post;
use FelixNagel\T3extblog\Domain\Repository\PostRepository;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Persistence\RepositoryInterface;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;

class PostMenuProcessor extends AbstractRecordMenuProcessor
{
    protected function getRepository(array $configuration): RepositoryInterface
    {
        return GeneralUtility::makeInstance(PostRepository::class);
    }

    protected function getUid(array $configuration, ContentObjectRenderer $cObj): ?int
    {
        return ($pid = PostController::isPostShowPage($cObj->getRequest())) === false ? null : $pid;
    }

    protected function getRecord(array $configuration, int $uid): ?array
    {
        /* @var $post Post */
        return ($post = $this->getRepository($configuration)->findByUid($uid)) === null ? null : $post->toArray();
    }
}
