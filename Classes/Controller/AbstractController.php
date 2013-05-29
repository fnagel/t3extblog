<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2009 Jochen Rau <jochen.rau@typoplanet.de>
 *  (c) 2011 Bastian Waidelich <bastian@typo3.org>
 *  (c) 2013 Felix Nagel <info@felixnagel.com>
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
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
 * Abstract base controller
 *
 * @package t3extblog
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 *
 */
abstract class Tx_T3extblog_Controller_AbstractController extends Tx_Extbase_MVC_Controller_ActionController {

	/**
	 * Logging Service
	 *
	 * @var Tx_T3extblog_Service_LoggingService
	 * @inject
	 */
	protected $log;

	
	/**
	 * Override getErrorFlashMessage to present
	 * nice flash error messages.
	 *
	 * @return string
	 */
	protected function getErrorFlashMessage() {
		$defaultFlashMessage = parent::getErrorFlashMessage();
		$locallangKey = sprintf('%s_%s_Error', $this->request->getControllerName(), ucfirst($this->actionMethodName));
		
		return $this->translate($locallangKey, $defaultFlashMessage);
	}

	/**
	 * helper function to render localized flashmessages
	 *
	 * @param string $action
	 * @param integer $severity optional severity code. One of the t3lib_FlashMessage constants
	 * @return void
	 */
	protected function addFlashMessage($key, $severity = t3lib_FlashMessage::OK) {
		$messageLocallangKey = sprintf('%s_%s_FlashMessage_%s', $this->request->getControllerName(), ucfirst($this->actionMethodName), $key);
		$localizedMessage = $this->translate($messageLocallangKey, '[' . $messageLocallangKey . ']');
		
		$titleLocallangKey = sprintf('%s_Title', $messageLocallangKey);
		$localizedTitle = $this->translate($titleLocallangKey, '[' . $titleLocallangKey . ']');
		
		$this->flashMessageContainer->add($localizedMessage, $localizedTitle, $severity);
	}

	/**
	 * helper function to use localized strings in BlogExample controllers
	 *
	 * @param string $key locallang key
	 * @param string $defaultMessage the default message to show if key was not found
	 * @return string
	 */
	protected function translate($key, $defaultMessage = '') {
		$message = Tx_Extbase_Utility_Localization::translate($key, 'T3ExtBlog');
		
		if ($message === NULL) {
			$message = $defaultMessage;
		}
		
		return $message;
	}

}

?>