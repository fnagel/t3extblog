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
 * Handles all notification mails
 * Configured by TYPO3 core log level
 *
 * @package t3extblog
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 *
 */
class Tx_T3extblog_Service_NotificationService implements t3lib_Singleton {
		
	/**
	 * @var Tx_Extbase_Object_ObjectManagerInterface
	 */
	protected $objectManager;

	/**
	 * subscriberRepository
	 *
	 * @var Tx_T3extblog_Domain_Repository_SubscriberRepository
	 */
	protected $subscriberRepository;

	/**
	 * Logging Service
	 *
	 * @var Tx_T3extblog_Service_LoggingService
	 */
	protected $log;
		
	/**
	 * @var Tx_T3extblog_Service_SettingsService
	 */
	protected $settingsService;

	/**
	 * @var array
	 */
	protected $settings;

	/**
	 * @var Tx_Extbase_Property_PropertyMapper $propertyMapper
	 */
	protected $propertyMapper;

	/**
	 * @param Tx_Extbase_Object_ManagerInterface $objectManager
	 * @return void
	 */
	public function injectObjectManager(Tx_Extbase_Object_ObjectManagerInterface $objectManager) {
		$this->objectManager = $objectManager;
	}
	
	/**
	 * Injects the Logging Service
	 *
	 * @param Tx_T3extblog_Service_LoggingService $loggingService
	 * @return void
	 */
	public function injectLoggingService(Tx_T3extblog_Service_LoggingService $loggingService) {
		$this->log = $loggingService;
	}
	
	/**
	 * Injects the Subscriber Repository
	 *
	 * @param Tx_T3extblog_Domain_Repository_SubscriberRepository $subscriberRepository 
	 * @return void
	 */
	public function injectSubscriberRepository(Tx_T3extblog_Domain_Repository_SubscriberRepository $subscriberRepository) {
		$this->subscriberRepository = $subscriberRepository;
	}

	/**
	 * Injects the Settings Service
	 *
	 * @param Tx_T3extblog_Service_SettingsService $settingsService 
	 * @return void
	 */
	public function injectSettingsService(Tx_T3extblog_Service_SettingsService $settingsService) {
		$this->settingsService = $settingsService;
	}
	
	/**
	 * @param Tx_Extbase_Property_PropertyMapper $propertyMapper
	 */
	public function injectPropertyMapper(Tx_Extbase_Property_PropertyMapper $propertyMapper) {
		$this->propertyMapper = $propertyMapper;
	}
	

	/**
	 * 
	 */
	public function initializeObject() {
		$this->settings = $this->settingsService->getTypoScriptSettings();	
	}
	
	
	/**
	 * Process new comment
	 *
	 * @param Tx_T3extblog_Domain_Model_Post $post The post the comment is related to
	 * @param Tx_T3extblog_Domain_Model_Comment $comment
	 * @return	void
	 */
	public function processAddedComment(Tx_T3extblog_Domain_Model_Comment $newComment) {
		$this->notifyAdmin($newComment);
		$this->notifySubscribers($newComment);		
		$this->processSubscription($newComment);		
	}
	
	/**
	 * Send 
	 *
	 * @param Tx_T3extblog_Domain_Model_Comment $comment
	 * @return	void
	 */
	private function processSubscription(Tx_T3extblog_Domain_Model_Comment $comment) {
		if ($this->settings['blogsystem']['comments']['subscribeForComments'] && $comment->getSubscribe()) {	
			// check if user already registered
			$subscriber = $this->subscriberRepository->findExistingSubscriptions($comment);
			if (count($subscriber) > 0) {
				$this->log->notice("Subscriber [" . $comment->getEmail() . "] already registered.");
				return;
			}		
			
			$post = $comment->getPost();			
			$this->log->dev("Send subscriber optin mail.");
			
			// add subscriber
			$subscriber	= $this->addSubscriber($comment);
			
			// send email
			$variables = array(
				'post' => $post,
				'comment' => $comment,
				'subscriber' => $subscriber				
			);		
			$emailBody = $this->renderEmailTemplate($variables, "SubscriberOptinMail.txt");
			$subject = "Subscribe to Blogpost: " . $post->getTitle();	
			
			$this->sendEmail(
				$subscriber->getMailTo(), 
				$this->settings['subscriptionManager']['subscriber']['mailFrom'], 
				$subject, 
				$emailBody
			);
		}
	}
	
	/**
	 * Send 
	 *
	 * @param Tx_T3extblog_Domain_Model_Comment $comment
	 * @return Tx_T3extblog_Domain_Model_Subscriber
	 */
	private function addSubscriber(Tx_T3extblog_Domain_Model_Comment $comment) {
		$this->log->dev("Add subscriber.");
		
		$newSubscriber = new Tx_T3extblog_Domain_Model_Subscriber($comment->getPostId());
		$newSubscriber->setEmail($comment->getEmail());
		$newSubscriber->setName($comment->getAuthor());
		$newSubscriber->updateAuth();
		
		$this->subscriberRepository->add($newSubscriber);
		$this->objectManager->get('Tx_Extbase_Persistence_Manager')->persistAll();	

		return $newSubscriber;
	}
	
	/**
	 * Send comment notification mails
	 * 
	 * @todo: only when last send is older than now?
	 *
	 * @param Tx_T3extblog_Domain_Model_Comment $comment
	 * @return	void
	 */
	private function notifySubscribers(Tx_T3extblog_Domain_Model_Comment $comment) {
		$settings = $this->settings['subscriptionManager']['subscriber'];
		
		if ($settings['enableNewCommentNotifications'] && $comment->isValid()) {
			$post = $comment->getPost();
			$this->log->dev("Send subscriber notification mails.");
			
			$subscribers = $this->subscriberRepository->findForNotification($post);
			$subject = "New Comment on: " . $post->getTitle();						
			
			foreach($subscribers as $subscriber) {
				// todo: needs testing
				$now = new DateTime();
				if ($now > $subscriber->getLastSent()) {
					$subscriber->updateAuth();

					$variables = array(
						'post' => $post,
						'comment' => $comment,
						'subscriber' => $subscriber
					);
					$emailBody = $this->renderEmailTemplate($variables, "SubscriberNewCommentMail.txt");

					$this->sendEmail($subscriber->getMailTo(), $settings['mailFrom'], $subject, $emailBody);
				}
			}			
		}
	}
	
	/**
	 * Send 
	 *
	 * @param Tx_T3extblog_Domain_Model_Comment $comment
	 * @return	void
	 */
	private function notifyAdmin(Tx_T3extblog_Domain_Model_Comment $comment) {
		$settings = $this->settings['subscriptionManager']['admin'];

		if ($settings['enable'] && is_array($settings['mailTo']) && count($settings['mailTo'])) {
			$post = $comment->getPost();
			$this->log->dev("Send admin notification mail.");
			
			$variables = array(
				'post' => $post,
				'comment' => $comment
			);		
			$emailBody = $this->renderEmailTemplate($variables, "AdminNewCommentMail.txt");
			$subject = "New Comment on Blogpost: " . $post->getTitle();			
			
			$this->sendEmail($settings['mailTo'], $settings['mailFrom'], $subject, $emailBody);	
		}
	}
	
	
	/**
	 * This is the main-function for sending Mails
	 *
	 */
	private function sendEmail($mailTo, $mailFrom, $subject, $emailBody) {
		if (!($mailTo && is_array($mailTo) && t3lib_div::validEmail(key($mailTo)))) {			
			$logData = array(
				'mailTo' => $mailTo,
				'mailFrom' => $mailFrom,
				'subject' => $subject
			);			
			$this->log->error("Given mailto email address is invalid.", $logData);
			
			return FALSE;
		}
		
		if (!($mailFrom && is_array($mailFrom) && t3lib_div::validEmail(key($mailFrom)))) {
			$mailFrom = t3lib_utility_Mail::getSystemFrom();
		}		
		
		$message = t3lib_div::makeInstance('t3lib_mail_Message');
		$message
			->setTo($mailTo)
			->setFrom($mailFrom)
			->setSubject($subject)
			->setCharset($GLOBALS['TSFE']->metaCharset)
			->setBody($emailBody, 'text/html')
			->addPart(strip_tags($emailBody), 'text/plain');
			
		if (!$this->settings["debug"]["disableEmailTransmission"]) {
			$message->send();
		} 
		$isSent = $message->isSent();

		$logData = array(
			'mailTo' => $mailTo,
			'mailFrom' => $mailFrom,
			'subject' => $subject,
			'emailBody' => $emailBody,
			'isSent' => $isSent
		);			
		$this->log->dev("Email sent.", $logData);

		return $isSent;
	}

	
	
	/**
	 * This functions renders template to use in Mails and Other views
	 *
	 * @param	array		Arguments for template
	 * @param	string		Choose a template (web or mail)
	 */
	private function renderEmailTemplate($variables, $templateFile = "Default.txt", $templateDirectory = "Email/") {	
		$frameworkConfig = $this->settingsService->getFrameworkSettings();
		$templateRootPath = t3lib_div::getFileAbsFileName($frameworkConfig['view']['templateRootPath']);
		$templatePathAndFilename = $templateRootPath . $templateDirectory . $templateFile;
		
		$emailView = $this->objectManager->create('Tx_Fluid_View_StandaloneView');
		
		$emailView->getRequest()->setPluginName('');
		$emailView->getRequest()->setControllerName('');
		$emailView->getRequest()->setControllerExtensionName('T3extblog'); // extension name for translate viewhelper
				
		$emailView->setLayoutRootPath(t3lib_div::getFileAbsFileName($frameworkConfig['view']['layoutRootPath']));
		$emailView->setTemplatePathAndFilename($templatePathAndFilename);
		$emailView->setPartialRootPath(t3lib_div::getFileAbsFileName($frameworkConfig['view']['partialRootPath']));
		$emailView->setFormat('txt');

		$emailView->assignMultiple($variables);
		$emailView->assignMultiple(array(
			'timestamp' => $GLOBALS['EXEC_TIME'],
			'domain' => t3lib_div::getIndpEnv('TYPO3_SITE_URL'),			
			'settings' => $this->settings			
		));
		
		return $emailView->render();
	}
	
}

?>