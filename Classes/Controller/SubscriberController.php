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
	 * Init actions
	 *
	 * @return void
	 */
	public function initializeAction() {
	}
	
	/**
	 * Displays a list of all posts a user subscibed to
	 *
	 * @return void
	 */
	public function listAction() {
		$this->checkAuth();
		
		$email = $this->feuserService->getEmail();		
		$subscribers = $this->subscriberRepository->findByEmail($email);
		
		$this->view->assign('email', $email);
		$this->view->assign('subscribers', $subscribers);
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
	
		if (!$this->request->hasArgument("email") || !$this->request->hasArgument("code")) {		
			$this->addFlashMessage('authNeeded');
			$this->redirect("error");
		}
		
		$email = $this->request->getArgument("email");
		$code = $this->request->getArgument("code");
		$this->requestAuth($email, $code);
	}
	
	/**
	 * Checks the given email and code, auth user if valid
	 *
	 * @param string $email The email address used for subscription
	 * @param string $code The hash code to be verified 
	 * @return void
	 */
	private function requestAuth($email, $code) {	
		// check parameter
		if (strlen($email) < 7 && strlen($code) < 32) {			
			$this->addFlashMessage('wrongLink');
			$this->redirect("error");
		}
	
		// check code
		$subscriber = $this->subscriberRepository->findOneByCode($code);
		if ($subscriber === NULL || $subscriber->getEmail() !== $email) {
			$this->addFlashMessage('authFailed');
			$this->redirect("error");
		}
		// todo add working timstamp check
		// if ($subscriber->getLastSent() + intval($this->settings['subscriber']['emailHashTimeout']) > time()) {
			// $this->addFlashMessage('linkOutdated');
			// $this->redirect("error");
		// }
		
		$this->feuserService->authValid();
		$this->feuserService->setEmail($email);
	}

	/**
	 * Error action
	 *
	 * @return void
	 */
	public function errorAction() {
	}

	/**
	 * action delete
	 *
	 * @param Tx_T3extblog_Domain_Model_Subscriber $subscriber
	 * @return void
	 */
	public function deleteAction(Tx_T3extblog_Domain_Model_Subscriber $subscriber) {
		$this->checkAuth();
		
		$this->subscriberRepository->remove($subscriber);
		$this->addFlashMessage('removed');
		$this->redirect('list');
	}

}
?>