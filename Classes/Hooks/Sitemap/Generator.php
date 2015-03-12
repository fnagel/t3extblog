<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2014-2015 Felix Nagel <info@felixnagel.com>
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
 * This class implements news sitemap
 * (http://www.google.com/support/webmasters/bin/answer.py?hl=en-nz&answer=42738)
 * for Google.
 *
 * The following URL parameters are expected:
 * - sitemap=blog
 * - singlePid=<uid of the blogsystem page>
 * - pidList=<comma-separated list of storage pids>
 * All pids must be in the rootline of the current pid. The safest way is to call
 * this site map from the root page of the site:
 * http://example.com/?eID=dd_googlesitemap&sitemap=news&singlePid=100&pidList=101,102,115
 *
 * If you need to show news on different single view pages, make several sitemaps
 * (it is possible with Google).
 *
 * @author	Felix Nagel <info@felixnagel.com>
 * @package	TYPO3
 * @subpackage	t3extblog
 */
class Tx_T3extblog_Hooks_Sitemap_Generator extends tx_ddgooglesitemap_ttnews {

	/**
	 * Creates an instance of this
	 *
	 * We cant use parent constructor as the wrong class will be initiated
	 */
	public function __construct() {
		$this->rendererClass = 'Tx_T3extblog_Hooks_Sitemap_Renderer';


		// taken from general renderer
		$this->cObj = t3lib_div::makeInstance('tslib_cObj');
		$this->cObj->start(array());

		$this->offset = max(0, intval(t3lib_div::_GET('offset')));
		$this->limit = max(0, intval(t3lib_div::_GET('limit')));
		if ($this->limit <= 0) {
			$this->limit = 100;
		}

		$this->createRenderer();

		// taken from ttnews renderer
		$singlePid = intval(t3lib_div::_GP('singlePid'));
		$this->singlePid = $singlePid && $this->isInRootline($singlePid) ? $singlePid : $GLOBALS['TSFE']->id;

		$this->validateAndcreatePageList();
	}

	/**
	 * Generates news site map.
	 *
	 * @return	void
	 */
	protected function generateSitemapContent() {
		if (count($this->pidList) > 0) {
			t3lib_div::loadTCA('tx_t3blog_post');

			$languageCondition = '';
			$language = t3lib_div::_GP('L');
			if (self::testInt($language)) {
				$languageCondition = ' AND sys_language_uid=' . $language;
			}

			/** @noinspection PhpUndefinedMethodInspection */
			$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*',
				'tx_t3blog_post', 'pid IN (' . implode(',', $this->pidList) . ')' .
//				' AND date>=' . (time() - 48 * 60 * 60) .
				$languageCondition .
				$this->cObj->enableFields('tx_t3blog_post'), '', 'date DESC',
				$this->offset . ',' . $this->limit
			);
			/** @noinspection PhpUndefinedMethodInspection */
			$rowCount = $GLOBALS['TYPO3_DB']->sql_num_rows($res);
			/** @noinspection PhpUndefinedMethodInspection */
			while (FALSE !== ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res))) {
				if (($url = $this->getPostItemUrl($row))) {
					echo $this->renderer->renderEntry($url, $row['title'], $row['date'], '', $row['tagClouds']);
				}
			}
			/** @noinspection PhpUndefinedMethodInspection */
			$GLOBALS['TYPO3_DB']->sql_free_result($res);

			if ($rowCount === 0) {
				echo '<!-- It appears that there are no tx_t3blog_post entries. If your ' .
					'blog storage sysfolder is outside of the rootline, you may ' .
					'want to use the dd_googlesitemap.skipRootlineCheck=1 TS ' .
					'setup option. Beware: it is insecure and may cause certain ' .
					'undesired effects! Better move your news sysfolder ' .
					'inside the rootline! -->';
			}
		}
	}

	/**
	 * Creates a link to the news item
	 *
	 * @param array	$row Post item
	 *
	 * @return	string
	 */
	protected function getPostItemUrl($row) {
		$date = new DateTime();
		$date->setTimestamp($row['date']);

		$linkParameters = t3lib_div::implodeArrayForUrl('tx_t3extblog_blogsystem', array(
			'post' => $row['uid'],
			'day' => $date->format('d'),
			'month' => $date->format('m'),
			'year' => $date->format('Y'),
		));

		$conf = array(
			'additionalParams' => $linkParameters,
			'forceAbsoluteUrl' => 1,
			'parameter' => $this->singlePid,
			'returnLast' => 'url',
			'useCacheHash' => TRUE,
		);
		$link = htmlspecialchars($this->cObj->typoLink('', $conf));

		return $link;
	}

}

