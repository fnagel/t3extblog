<?php

namespace TYPO3\T3extblog\Controller;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2013-2015 Felix Nagel <info@felixnagel.com>
 *
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

use TYPO3\CMS\Extbase\Mvc\Exception as MvcException;
use TYPO3\CMS\Extbase\Mvc\Exception\InvalidArgumentValueException;
use TYPO3\T3extblog\Domain\Model\PostSubscriber;

/**
 * SubscriberController
 */
class PostSubscriberController extends AbstractSubscriberController {

	/**
	 * subscriberRepository
	 *
	 * @var \TYPO3\T3extblog\Domain\Repository\PostSubscriberRepository
	 * @inject
	 */
	protected $subscriberRepository;

	/**
	 * blogSubscriberRepository
	 *
	 * @var \TYPO3\T3extblog\Domain\Repository\BlogSubscriberRepository
	 * @inject
	 */
	protected $blogSubscriberRepository;

	/**
	 * subscriber
	 *
	 * @var \TYPO3\T3extblog\Domain\Model\PostSubscriber
	 */
	protected $subscriber = NULL;

	/**
	 * @inheritdoc
	 *
	 * @return void
	 */
	protected function initializeAction() {
		parent::initializeAction();

		$this->subscriptionSettings = $this->settings['subscriptionManager']['comment']['subscriber'];
	}

	/**
	 * action list
	 *
	 * @return void
	 */
	public function listAction() {
		$this->checkAuth();

		$this->redirect('list', 'Subscriber');
	}

	/**
	 * action delete
	 *
	 * @param PostSubscriber $subscriber
	 *
	 * @throws InvalidArgumentValueException
	 * @return void
	 */
	public function deleteAction(PostSubscriber $subscriber = NULL) {
		parent::deleteAction($subscriber);
	}

	/**
	 * Finds existing subscriptions
	 *
	 * @param PostSubscriber $subscriber
	 * @return array|\TYPO3\CMS\Extbase\Persistence\QueryResultInterface
	 */
	protected function findExistingSubscriptions($subscriber) {
		return $this->subscriberRepository->findExistingSubscriptions(
			$subscriber->getPostUid(),
			$subscriber->getEmail(),
			$subscriber->getUid()
		);
	}

}
