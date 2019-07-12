<?php

namespace FelixNagel\T3extblog\Hooks;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2013-2018 Felix Nagel <info@felixnagel.com>
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
 * RealUrl auto config.
 */
class RealUrl
{
    /**
     * Generates additional RealURL configuration
     * and merges it with provided configuration.
     *
     * @param array $params Default configuration
     * @param       $ref
     *
     * @internal param \tx_realurl_autoconfgen $pObj Parent object
     *
     * @return array Updated configuration
     */
    public function extensionConfiguration($params, &$ref)
    {
        return array_merge_recursive($params['config'], [
            'postVarSets' => [
                '_DEFAULT' => [
                    't3extblog-action' => [
                        [
                            'GETvar' => 'tx_t3extblog_blogsystem[action]',
                            'noMatch' => 'bypass',
                        ],
                    ],

                    'article' => [
                        [
                            'GETvar' => 'tx_t3extblog_blogsystem[year]',
                        ],
                        [
                            'GETvar' => 'tx_t3extblog_blogsystem[month]',
                        ],
                        [
                            'GETvar' => 'tx_t3extblog_blogsystem[day]',
                        ],
                        [
                            'GETvar' => 'tx_t3extblog_blogsystem[post]',
                            'lookUpTable' => [
                                'table' => 'tx_t3blog_post',
                                'id_field' => 'uid',
                                'alias_field' => 'title',
                                'addWhereClause' => ' AND NOT deleted AND NOT hidden',
                                'useUniqueCache' => 1,
                                'useUniqueCache_conf' => [
                                    'strtolower' => 1,
                                    'spaceCharacter' => '-',
                                ],
                                'enable404forInvalidAlias' => 1,
                                'autoUpdate' => 1,
                                'expireDays' => 180,
                                // language support (translated urls)
                                'languageGetVar' => 'L',
                                'languageExceptionUids' => '',
                                'languageField' => 'sys_language_uid',
                                'transOrigPointerField' => 'l18n_parent',
                            ],
                        ],
                    ],

                    // this is sufficient because we only need to change the controller keyword
                    // as create is the default action for comment controller
                    'comment' => [
                        [
                            'GETvar' => 'tx_t3extblog_blogsystem[controller]',
                            'noMatch' => 'bypass',
                            'valueMap' => [
                                'new' => 'Comment',
                            ],
                        ],
                    ],

                    'permalink' => [
                        [
                            'GETvar' => 'tx_t3extblog_blogsystem[permalinkPost]',
                        ],
                    ],

                    'preview' => [
                        [
                            'GETvar' => 'tx_t3extblog_blogsystem[previewPost]',
                        ],
                    ],

                    'author' => [
                        [
                            'GETvar' => 'tx_t3extblog_blogsystem[author]',
                            'lookUpTable' => [
                                'table' => 'be_users',
                                'id_field' => 'uid',
                                'alias_field' => 'username',
                                'addWhereClause' => ' AND deleted !=1 AND disable !=1',
                                'useUniqueCache' => 1,
                                'useUniqueCache_conf' => [
                                    'strtolower' => 1,
                                    'spaceCharacter' => '-',
                                ],
                                'enable404forInvalidAlias' => 1,
                                'autoUpdate' => 1,
                                'expireDays' => 180,
                            ],
                        ],
                    ],

                    'tags' => [
                        [
                            'GETvar' => 'tx_t3extblog_blogsystem[tag]',
                        ],
                    ],

                    'category' => [
                        [
                            'GETvar' => 'tx_t3extblog_blogsystem[category]',
                            'lookUpTable' => [
                                'table' => 'tx_t3blog_cat',
                                'id_field' => 'uid',
                                'alias_field' => 'catname',
                                'addWhereClause' => ' AND deleted !=1 AND hidden !=1',
                                'useUniqueCache' => 1,
                                'useUniqueCache_conf' => [
                                    'strtolower' => 1,
                                    'spaceCharacter' => '-',
                                ],
                                'enable404forInvalidAlias' => 1,
                                'autoUpdate' => 1,
                                'expireDays' => 180,
                                // language support (translated urls)
                                'languageGetVar' => 'L',
                                'languageExceptionUids' => '',
                                'languageField' => 'sys_language_uid',
                                'transOrigPointerField' => 'l18n_parent',
                            ],
                        ],
                    ],

                    'page' => [
                        [
                            'GETvar' => 'tx_t3extblog_blogsystem[@widget_0][currentPage]',
                        ],
                    ],

                    'subscription' => [
                        [
                            'GETvar' => 'tx_t3extblog_subscriptionmanager[controller]',
                            'valueMap' => [
                                'blog' => 'BlogSubscriber',
                                'post' => 'PostSubscriber',
                            ],
                            'noMatch' => 'bypass',
                        ],
                        [
                            'GETvar' => 'tx_t3extblog_subscriptionmanager[action]',
                            'valueMap' => [
                                'confirmation' => 'confirm',
                                'create' => 'create',
                                'delete' => 'delete',
                                'error' => 'error',
                                'logout' => 'logout',
                            ],
                            'noMatch' => 'bypass',
                        ],
                        [
                            'GETvar' => 'tx_t3extblog_subscriptionmanager[code]',
                        ],
                    ],

                    'subscription-blog' => [
                        [
                            'GETvar' => 'tx_t3extblog_blogsubscription[action]',
                        ],
                    ],
                ],
            ],
        ]);
    }
}
