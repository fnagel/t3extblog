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
     * @param string $code
     * @param bool   $enableFields
     *
     * @return AbstractSubscriber
     */
    public function findByCode($code, $enableFields = true)
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
     * @param QueryInterface $query
     * @param string         $email
     * @param int            $excludeUid
     *
     * @return array
     */
    protected function getBasicExistingSubscriptionConstraints(QueryInterface $query, $email, $excludeUid = null)
    {
        $constraints = [];

        $constraints[] = $query->equals('email', $email);

        if ($excludeUid !== null) {
            $constraints[] = $query->logicalNot($query->equals('uid', (int) $excludeUid));
        }

        return $constraints;
    }
}
