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
class Tx_T3extblog_Controller_SubscriberController extends Tx_T3extblog_Controller_AbstractController {

	/**
	 * subscriberRepository
	 *
	 * @var Tx_T3extblog_Domain_Repository_SubscriberRepository
	 * @inject
	 */
	protected $subscriberRepository;

	/**
	 * postRepository
	 *
	 * @var Tx_T3extblog_Domain_Repository_PostRepository
	 * @inject
	 */
	protected $postRepository;
	
	/**
	 * feuserService
	 *
	 * @var Tx_T3extblog_Service_FrontendUserService
	 * @inject
	 */
	protected $feuserService;
		
	/**
	 * objectManager
	 *
	 * @var Tx_Extbase_Object_ObjectManagerInterface
	 * @inject
	 */
	protected $objectManager;
		
	/**
	 * Displays a list of all posts a user subscribed to
	 *
	 * @return void
	 */
	public function listAction() {
		$this->checkAuth();
		
		$email = $this->feuserService->getDataByKey("subscriber_email");
		$subscribers = $this->subscriberRepository->findByEmail($email);
		
		$this->view->assign('email', $email);
		$this->view->assign('subscribers', $subscribers);
	}

	/**
	 * action confirm
	 *
	 * @return void
	 */
	public function confirmAction() {
		$this->checkAuth();
		
		$subscriber = $this->subscriberRepository->findForConfirmation($this->feuserService->getDataByKey("subscriber_uid"));		
		$subscriber->_setProperty("hidden", FALSE);
		$this->objectManager->get('Tx_Extbase_Persistence_Manager')->persistAll();	
				
		$this->addFlashMessage('Confirmed', t3lib_FlashMessage::NOTICE);
		$this->redirect('list');
	}
	
	/**
	 * action delete
	 *
	 * @param Tx_T3extblog_Domain_Model_Subscriber $subscriber
	 * @return void
	 */
	public function deleteAction(Tx_T3extblog_Domain_Model_Subscriber $subscriber = NULL) {
		$this->checkAuth();
		
		if ($subscriber === NULL) {			
			$subscriber = $this->subscriberRepository->findForConfirmation($this->feuserService->getDataByKey("subscriber_uid"));
		}
		
		$this->subscriberRepository->remove($subscriber);
		$this->objectManager->get('Tx_Extbase_Persistence_Manager')->persistAll();		
		
		$this->addFlashMessage('Deleted', t3lib_FlashMessage::NOTICE);		
		$this->redirect('list');
	}

	/**
	 * Error action
	 *
	 * @return void
	 */
	public function errorAction() {
	}

	/**
	 * Checks if auth needed
	 *
	 * @return void
	 */
	private function checkAuth() {	
		if ($this->feuserService->hasAuth()) {
			return;		
		}	
	
		if (!$this->request->hasArgument("code")) {		
			$this->addFlashMessage('AuthNeeded', t3lib_FlashMessage::NOTICE);
			$this->redirect("error");
		}
		
		$code = $this->request->getArgument("code");
		$this->requestAuth($code);
	}
	
	/**
	 * Checks the given email and code, auth user if valid
	 *
	 * @param string $email The email address used for subscription
	 * @param string $code The hash code to be verified 
	 * @return void
	 */
	private function requestAuth($code) {	
		// check parameter
		if (strlen($code) < 32) {			
			$this->addFlashMessage('WrongLink', t3lib_FlashMessage::ERROR);
			$this->redirect("error");
		}
	
		// check code
		$subscriber = $this->subscriberRepository->findOneByCode($code);
		if ($subscriber === NULL) {
			$this->addFlashMessage('AuthFailed', t3lib_FlashMessage::ERROR);
			$this->redirect("error");
		}
		
		// todo add working timstamp check
		// if ($subscriber->getLastSent() + intval($this->settings['subscriber']['emailHashTimeout']) > time()) {
			// $this->addFlashMessage('LinkOutdated', t3lib_FlashMessage::ERROR);
			// $this->redirect("error");
		// }
		
		$this->feuserService->authValid();
		$this->feuserService->setData(array(
			"subscriber_email" => $subscriber->getEmail(),
			"subscriber_uid" => $subscriber->getUid()			
		));
	}

}
?>