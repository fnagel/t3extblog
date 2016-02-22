<?php

namespace TYPO3\T3extblog\Hooks;

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

/**
 * RealUrl auto config
 */
class RealUrl {

	/**
	 * Generates additional RealURL configuration
	 * and merges it with provided configuration
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
							'noMatch' => 'bypass',
						),
					),

					'article' => array(
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
								'enable404forInvalidAlias' => 1,
								'autoUpdate' => 1,
								'expireDays' => 180,
								// language support (translated urls)
								'languageGetVar' => 'L',
								'languageExceptionUids' => '',
								'languageField' => 'sys_language_uid',
								'transOrigPointerField' => 'l18n_parent',
							),
						),
					),

					// this is sufficient because we only need to change the controller keyword
					// as create is the default action for comment controller
					'comment' => array(
						array(
							'GETvar' => 'tx_t3extblog_blogsystem[controller]',
							'noMatch' => 'bypass',
							'valueMap' => array(
								'new' => 'Comment',
							),
						),
					),

					'permalink' => array(
						array(
							'GETvar' => 'tx_t3extblog_blogsystem[permalinkPost]',
						),
					),

					'preview' => array(
						array(
							'GETvar' => 'tx_t3extblog_blogsystem[previewPost]',
						),
					),

					'author' => array(
						array(
							'GETvar' => 'tx_t3extblog_blogsystem[author]',
							'lookUpTable' => array(
								'table' => 'be_users',
								'id_field' => 'uid',
								'alias_field' => 'username',
								'addWhereClause' => ' AND deleted !=1 AND disable !=1',
								'useUniqueCache' => 1,
								'useUniqueCache_conf' => array(
									'strtolower' => 1,
									'spaceCharacter' => '-',
								),
								'enable404forInvalidAlias' => 1,
								'autoUpdate' => 1,
								'expireDays' => 180,
							)
						)
					),

					'tags' => array(
						array(
							'GETvar' => 'tx_t3extblog_blogsystem[tag]',
						),
					),

					'category' => array(
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
								),
								'enable404forInvalidAlias' => 1,
								'autoUpdate' => 1,
								'expireDays' => 180,
								// language support (translated urls)
								'languageGetVar' => 'L',
								'languageExceptionUids' => '',
								'languageField' => 'sys_language_uid',
								'transOrigPointerField' => 'l18n_parent',
							)
						)
					),

					'page' => array(
						array(
							'GETvar' => 'tx_t3extblog_blogsystem[@widget_0][currentPage]',
						),
						array(
							'GETvar' => 'tx_t3extblog_blogsystem[@widget_0][addQueryStringMethod]',
						),
					),

					'subscription' => array(
						array(
							'GETvar' => 'tx_t3extblog_subscriptionmanager[controller]',
							'valueMap' => array(
								'blog' => 'BlogSubscriber',
								'post' => 'PostSubscriber',
							),
							'noMatch' => 'bypass',
						),
						array(
							'GETvar' => 'tx_t3extblog_subscriptionmanager[action]',
							'valueMap' => array(
								'confirmation' => 'confirm',
								'create' => 'create',
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

					'subscription-blog' => array(
						array(
							'GETvar' => 'tx_t3extblog_blogsubscription[action]',
						),
					),
				),
			),
		));
	}
}
