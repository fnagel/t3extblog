<?php

/*                                                                        *
 * This script is backported from the FLOW3 package "TYPO3.Fluid".        *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the GNU Lesser General Public License, either version 3   *
 *  of the License, or (at your option) any later version.                *
 *                                                                        *
 *                                                                        *
 * This script is distributed in the hope that it will be useful, but     *
 * WITHOUT ANY WARRANTY; without even the implied warranty of MERCHAN-    *
 * TABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Lesser       *
 * General Public License for more details.                               *
 *                                                                        *
 * You should have received a copy of the GNU Lesser General Public       *
 * License along with the script.                                         *
 * If not, see http://www.gnu.org/licenses/lgpl.html                      *
 *                                                                        *
 * The TYPO3 project - inspiring people to share!                         *
 *                                                                        */

/**
 * View helper which renders the flash messages (if there are any) as an unsorted list.
 */
class Tx_T3extblog_ViewHelpers_Frontend_FlashMessagesViewHelper extends Tx_Fluid_Core_ViewHelper_AbstractTagBasedViewHelper {

	/**
	 * @var array
	 */
	protected $severityMapping = array(
		t3lib_FlashMessage::NOTICE => 'alert-info',
		t3lib_FlashMessage::INFO => 'alert-info',
		t3lib_FlashMessage::OK => 'alert-success',
		t3lib_FlashMessage::WARNING => 'alert-warning',
		t3lib_FlashMessage::ERROR => 'alert-error'
	);

	/**
	 * @var tslib_cObj
	 */
	protected $contentObject;

	/**
	 * @var Tx_Extbase_Configuration_ConfigurationManagerInterface
	 */
	protected $configurationManager;

	/**
	 * @param Tx_Extbase_Configuration_ConfigurationManagerInterface $configurationManager
	 *
	 * @return void
	 */
	public function injectConfigurationManager(Tx_Extbase_Configuration_ConfigurationManagerInterface $configurationManager) {
		$this->configurationManager = $configurationManager;
		$this->contentObject = $this->configurationManager->getContentObject();
	}

	/**
	 * Initialize arguments
	 *
	 * @return void
	 */
	public function initializeArguments() {
		$this->registerUniversalTagAttributes();
	}

	/**
	 * Renders FlashMessages and flushes the FlashMessage queue
	 * Note: This disables the current page cache in order to prevent FlashMessage output
	 * from being cached.
	 * @see tslib_fe::no_cache
	 *
	 * @return string rendered Flash Messages, if there are any.
	 */
	public function render() {
		$flashMessages = $this->controllerContext->getFlashMessageContainer()->getAllMessagesAndFlush();

		if ($flashMessages === NULL || count($flashMessages) === 0) {
			return '';
		}

		if (isset($GLOBALS['TSFE']) && $this->contentObject->getUserObjectType() === tslib_cObj::OBJECTTYPE_USER) {
			$GLOBALS['TSFE']->no_cache = 1;
		}

		return $this->renderFlashMessages($flashMessages);
	}

	/**
	 * Renders the flash messages in bootstrap style
	 *
	 * @param array $flashMessages array<t3lib_FlashMessage>
	 *
	 * @return string
	 */
	protected function renderFlashMessages(array $flashMessages) {
		$this->tag->setTagName('div');

		$tagContent = '';
		$tagClass = 'alert alert-block';

		/* @var $singleFlashMessage t3lib_FlashMessage */
		foreach ($flashMessages as $singleFlashMessage) {
			$tagContent .= $this->renderFlashMessage($singleFlashMessage);
			$tagClass .= ' ' . $this->getSeverityClass($singleFlashMessage->getSeverity());
		}

		$this->tag->setContent($tagContent);
		$this->tag->addAttribute('class', $tagClass);

		return $this->tag->render();
	}

	/**
	 * @param t3lib_FlashMessage $flashMessage array<t3lib_FlashMessage>
	 *
	 * @return string
	 */
	protected function renderFlashMessage(t3lib_FlashMessage $flashMessage) {
		$content = '<h5>' . $flashMessage->getTitle() . '</h5>';
		$content .= '<p>' . $flashMessage->getMessage() . '</p>';

		return $content;
	}

	/**
	 * @param integer $severity
	 *
	 * @return string
	 */
	protected function getSeverityClass($severity) {
		if (array_key_exists($severity, $this->severityMapping)) {
			return $this->severityMapping[$severity];
		}

		return '';
	}
}

?>
