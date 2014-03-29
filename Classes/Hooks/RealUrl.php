<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2013-2014 Felix Nagel <info@felixnagel.com>
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
 * RealUrl auto config
 *
 * @package t3extblog
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 *
 */
class Tx_T3extblog_Hooks_RealUrl {

	public function encodeSpURL_postProc(&$params, &$ref) {
		$params['URL'] = str_replace('t3extblog-action/permalink/', 'permalink/', $params['URL']);
		$params['URL'] = str_replace('t3extblog-action/preview//', 'preview/', $params['URL']);
	}
	public function decodeSpURL_preProc(&$params, &$ref) {
		$params['URL'] = str_replace('permalink/', 't3extblog-action/permalink/', $params['URL']);
		$params['URL'] = str_replace('preview/', 't3extblog-action/preview//', $params['URL']);
	}

	/**
	 *
	 *
	 * @param    array $params Default configuration
	 * @param          $ref
	 *
	 * @internal param \tx_realurl_autoconfgen $pObj Parent object
	 *
	 * @return    array                        Updated configuration
	 */
	public function postProcessConfiguration(&$params, &$ref) {
		$params['config'] = array_merge_recursive($params['config'], array(
			'encodeSpURL_postProc' => array(
				't3extblog' => 'EXT:t3extblog/Classes/Hooks/RealUrl.php:Tx_T3extblog_Hooks_RealUrl->encodeSpURL_postProc',
			),
			'decodeSpURL_preProc' => array(
				't3extblog' => 'EXT:t3extblog/Classes/Hooks/RealUrl.php:Tx_T3extblog_Hooks_RealUrl->decodeSpURL_preProc',
			),
		));
	}

	/**
	 * Generates additional RealURL configuration and merges it with provided configuration
	 *
	 * @param    array $params Default configuration
	 * @param          $ref
	 *
	 * @internal param \tx_realurl_autoconfgen $pObj Parent object
	 *
	 * @return    array                        Updated configuration
	 */
	public function extensionConfiguration($params, &$ref) {
		return array_merge_recursive($params['config'], array(
			'postVarSets' => array(
				'_DEFAULT' => array(
					't3extblog-action' => array(
						array(
							'GETvar' => 'tx_t3extblog_blogsystem[action]',
							'valueMap' => array(
								'permalink' => 'permalink',
								'preview' => 'preview',
							),
							'noMatch' => 'bypass',
						),

						array(
							'GETvar' => 'tx_t3extblog_blogsystem[permalinkPost]',
						),

						array(
							'GETvar' => 'tx_t3extblog_blogsystem[previewPost]',
						),
					),

					'artikel' => array(
						array(
							'GETvar' => 'tx_t3extblog_blogsystem[year]',
						),
						array(
							'GETvar' => 'tx_t3extblog_blogsystem[month]',
						),
						array(
							'GETvar' => 'tx_t3extblog_blogsystem[day]',
						),
						array(
							'GETvar' => 'tx_t3extblog_blogsystem[post]',
							'lookUpTable' => array(
								'table' => 'tx_t3blog_post',
								'id_field' => 'uid',
								'alias_field' => 'title',
								'addWhereClause' => ' AND NOT deleted AND NOT hidden',
								'useUniqueCache' => 1,
								'useUniqueCache_conf' => array(
									'strtolower' => 1,
									'spaceCharacter' => '-',
								),
							),
						),
					),

					'tags' => array(
						array(
							'GETvar' => 'tx_t3extblog_blogsystem[tag]',
						),
					),

					'kategorie' => array(
						array(
							'GETvar' => 'tx_t3extblog_blogsystem[category]',
							'lookUpTable' => array(
								'table' => 'tx_t3blog_cat',
								'id_field' => 'uid',
								'alias_field' => 'catname',
								'addWhereClause' => ' AND deleted !=1 AND hidden !=1',
								'useUniqueCache' => 1,
								'useUniqueCache_conf' => array(
									'strtolower' => 1,
									'spaceCharacter' => '-',
								)
							)
						)
					),

					'seite' => array(
						array(
							'GETvar' => 'tx_t3extblog_blogsystem[@widget_0][currentPage]',
						),
					),

					'subscription' => array(
						array(
							'GETvar' => 'tx_t3extblog_subscriptionmanager[action]',
							'valueMap' => array(
								'confirmation' => 'confirm',
								'delete' => 'delete',
								'error' => 'error',
								'logout' => 'logout',
							),
							'noMatch' => 'bypass',
						),
						array(
							'GETvar' => 'tx_t3extblog_subscriptionmanager[code]',
						),
					),
				),
			),
		));
	}

}

?>