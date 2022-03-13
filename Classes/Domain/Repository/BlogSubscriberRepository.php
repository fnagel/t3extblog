<?php

namespace FelixNagel\T3extblog\Domain\Repository;

use TYPO3\CMS\Extbase\Persistence\QueryInterface;
use TYPO3\CMS\Extbase\Persistence\QueryResultInterface;

/**
 * This file is part of the "t3extblog" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

/**
 * BlogSubscriberRepository.
 */
class BlogSubscriberRepository extends AbstractSubscriberRepository
{
    public function createQuery(int $pageUid = null): QueryInterface
    {
        $query = parent::createQuery($pageUid);

        $query->getQuerySettings()->setRespectSysLanguage(false);

        return $query;
    }

    public function findForNotification(): QueryResultInterface
    {
        return $this->createQuery()->execute();
    }

    /**
     * Search for already registered subscriptions.
     */
    public function findExistingSubscriptions(string $email, int $excludeUid = null): QueryResultInterface
    {
        $query = $this->createQuery();

        $constraints = $this->getBasicExistingSubscriptionConstraints($query, $email, $excludeUid);

        $query->matching(
            $query->logicalAnd($constraints)
        );

        return $query->execute();
    }
}
