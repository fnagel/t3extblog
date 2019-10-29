<?php

namespace FelixNagel\T3extblog\Service;

/**
 * This file is part of the "t3extblog" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

/**
 * Interface for Notification Service.
 *
 * This class should send emails (admin, comment author opt-in, subscription)
 * when receiving new or changed comments.
 */
interface NotificationServiceInterface
{
    /**
     * Process added entity.
     *
     * @param object $entity
     */
    public function processNewEntity($entity);

    /**
     * Process changed status of a entity.
     *
     * @param object $entity
     */
    public function processChangedStatus($entity);
}
