<?php

namespace FelixNagel\T3extblog\Domain\Repository;

/**
 * This file is part of the "t3extblog" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use TYPO3\CMS\Extbase\Persistence\QueryInterface;
use FelixNagel\T3extblog\Domain\Model\AbstractSubscriber;

/**
 * AbstractSubscriberRepository.
 */
abstract class AbstractSubscriberRepository extends AbstractRepository
{
    protected $defaultOrderings = [
        'crdate' => QueryInterface::ORDER_DESCENDING,
    ];

    /**
     * Find by code.
     *
     *
     */
    public function findByCode(string $code, bool $enableFields = true): AbstractSubscriber
    {
        $query = $this->createQuery();
        $query->getQuerySettings()->setIgnoreEnableFields(!$enableFields);

        $query->matching(
            $query->logicalAnd([
                $query->equals('code', $code),
                $query->equals('deleted', 0),
            ])
        );

        return $query->execute()->getFirst();
    }

    /**
     * @param int            $excludeUid
     *
     */
    protected function getBasicExistingSubscriptionConstraints(QueryInterface $query, string $email, int $excludeUid = null): array
    {
        $constraints = [];

        $constraints[] = $query->equals('email', $email);

        if ($excludeUid !== null) {
            $constraints[] = $query->logicalNot($query->equals('uid', (int) $excludeUid));
        }

        return $constraints;
    }
}
