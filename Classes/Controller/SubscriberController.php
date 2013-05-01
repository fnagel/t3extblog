<?php

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2013 Felix Nagel <info@felixnagel.com>
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

/**
 *
 *
 * @package t3extblog
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 *
 */
class Tx_T3extblog_Controller_SubscriberController extends Tx_Extbase_MVC_Controller_ActionController {

	/**
	 * subscriberRepository
	 *
	 * @var Tx_T3extblog_Domain_Repository_SubscriberRepository
	 */
	protected $subscriberRepository;

	/**
	 * injectSubscriberRepository
	 *
	 * @param Tx_T3extblog_Domain_Repository_SubscriberRepository $subscriberRepository
	 * @return void
	 */
	public function injectSubscriberRepository(Tx_T3extblog_Domain_Repository_SubscriberRepository $subscriberRepository) {
		$this->subscriberRepository = $subscriberRepository;
	}

	/**
	 * action list
	 *
	 * @return void
	 */
	public function listAction() {
		$subscribers = $this->subscriberRepository->findAll();
		$this->view->assign('subscribers', $subscribers);
	}

	/**
	 * action new
	 *
	 * @param Tx_T3extblog_Domain_Model_Subscriber $newSubscriber
	 * @dontvalidate $newSubscriber
	 * @return void
	 */
	public function newAction(Tx_T3extblog_Domain_Model_Subscriber $newSubscriber = NULL) {
		$this->view->assign('newSubscriber', $newSubscriber);
	}

	/**
	 * action create
	 *
	 * @param Tx_T3extblog_Domain_Model_Subscriber $newSubscriber
	 * @return void
	 */
	public function createAction(Tx_T3extblog_Domain_Model_Subscriber $newSubscriber) {
		$this->subscriberRepository->add($newSubscriber);
		$this->flashMessageContainer->add('Your new Subscriber was created.');
		$this->redirect('list');
	}

	/**
	 * action edit
	 *
	 * @param Tx_T3extblog_Domain_Model_Subscriber $subscriber
	 * @return void
	 */
	public function editAction(Tx_T3extblog_Domain_Model_Subscriber $subscriber) {
		$this->view->assign('subscriber', $subscriber);
	}

	/**
	 * action update
	 *
	 * @param Tx_T3extblog_Domain_Model_Subscriber $subscriber
	 * @return void
	 */
	public function updateAction(Tx_T3extblog_Domain_Model_Subscriber $subscriber) {
		$this->subscriberRepository->update($subscriber);
		$this->flashMessageContainer->add('Your Subscriber was updated.');
		$this->redirect('list');
	}

	/**
	 * action delete
	 *
	 * @param Tx_T3extblog_Domain_Model_Subscriber $subscriber
	 * @return void
	 */
	public function deleteAction(Tx_T3extblog_Domain_Model_Subscriber $subscriber) {
		$this->subscriberRepository->remove($subscriber);
		$this->flashMessageContainer->add('Your Subscriber was removed.');
		$this->redirect('list');
	}

}
?>