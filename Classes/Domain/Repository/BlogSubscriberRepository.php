<?php

namespace FelixNagel\T3extblog\Domain\Repository;

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
    /**
     * @param null $pageUid
     *
     * @return \TYPO3\CMS\Extbase\Persistence\QueryInterface
     */
    public function createQuery($pageUid = null)
    {
        $query = parent::createQuery($pageUid);

        $query->getQuerySettings()->setRespectSysLanguage(false);

        return $query;
    }

    /**
     * @return array|\TYPO3\CMS\Extbase\Persistence\QueryResultInterface
     */
    public function findForNotification()
    {
        $query = $this->createQuery();

        return $query->execute();
    }

    /**
     * Search for already registered subscriptions.
     *
     * @param string $email
     * @param int    $excludeUid
     *
     * @return array|\TYPO3\CMS\Extbase\Persistence\QueryResultInterface
     */
    public function findExistingSubscriptions($email, $excludeUid = null)
    {
        $query = $this->createQuery();

        $constraints = $this->getBasicExistingSubscriptionConstraints($query, $email, $excludeUid);

        $query->matching(
            $query->logicalAnd($constraints)
        );

        return $query->execute();
    }
}
