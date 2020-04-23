<?php

namespace FelixNagel\T3extblog\Dashboard\Provider;

/**
 * This file is part of the "t3extblog" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

class DraftPostListDataProvider extends AbstractPostListDataProvider
{
    public function getItems(): array
    {
        return $this->postRepository->findDrafts($this->getStoragePids())->toArray();
    }
}
