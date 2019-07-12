<?php

namespace FelixNagel\T3extblog\Hooks\Sitemap;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2014-2018 Felix Nagel <info@felixnagel.com>
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

use TYPO3\CMS\Core\Utility\GeneralUtility;
use DmitryDulepov\DdGooglesitemap\Generator\TtNewsSitemapGenerator;
use TYPO3\CMS\Core\Utility\MathUtility;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;

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
 */
class Generator extends TtNewsSitemapGenerator
{
    /**
     * Creates an instance of this.
     *
     * We cant use parent constructor as the wrong class will be initiated
     */
    public function __construct()
    {
        $this->rendererClass = Renderer::class;

        $this->cObj = GeneralUtility::makeInstance(ContentObjectRenderer::class);
        $this->cObj->start([]);

        $this->offset = max(0, (int) GeneralUtility::_GET('offset'));
        $this->limit = max(0, (int) GeneralUtility::_GET('limit'));
        if ($this->limit <= 0) {
            $this->limit = 100;
        }

        $this->createRenderer();

        $singlePid = intval(GeneralUtility::_GP('singlePid'));
        $this->singlePid = $singlePid && $this->isInRootline($singlePid) ?
            $singlePid : \FelixNagel\T3extblog\Utility\GeneralUtility::getTsFe()->id;

        $this->validateAndcreatePageList();
    }

    /**
     * Generates news site map.
     */
    protected function generateSitemapContent()
    {
        if (count($this->pidList) > 0) {
            $languageCondition = '';
            $language = GeneralUtility::_GP('L');
            if (MathUtility::canBeInterpretedAsInteger($language)) {
                $languageCondition = ' AND sys_language_uid='.$language;
            }

            $whereClause = 'pid IN ('.implode(',', $this->pidList).')'.
                $languageCondition.
                $this->cObj->enableFields('tx_t3blog_post');

            /* @noinspection PhpUndefinedMethodInspection */
            $res = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
                '*',
                'tx_t3blog_post',
                $whereClause,
                '',
                'date DESC',
                $this->offset.','.$this->limit
            );
            /* @noinspection PhpUndefinedMethodInspection */
            $rowCount = $GLOBALS['TYPO3_DB']->sql_num_rows($res);
            /* @noinspection PhpUndefinedMethodInspection */
            while (false !== ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res))) {
                if (($url = $this->getPostItemUrl($row))) {
                    echo $this->renderer->renderEntry($url, $row['title'], $row['date'], '', $row['tagClouds']);
                }
            }
            /* @noinspection PhpUndefinedMethodInspection */
            $GLOBALS['TYPO3_DB']->sql_free_result($res);

            if ($rowCount === 0) {
                echo '<!-- It appears that there are no tx_t3blog_post entries. If your '.
                    'blog storage sysfolder is outside of the rootline, you may '.
                    'want to use the dd_googlesitemap.skipRootlineCheck=1 TS '.
                    'setup option. Beware: it is insecure and may cause certain '.
                    'undesired effects! Better move your news sysfolder '.
                    'inside the rootline! -->';
            }
        }
    }

    /**
     * Creates a link to the news item.
     *
     * @param array $row Post item
     *
     * @return string
     */
    protected function getPostItemUrl($row)
    {
        $date = new \DateTime();
        $date->setTimestamp($row['date']);

        $linkParameters = GeneralUtility::implodeArrayForUrl('tx_t3extblog_blogsystem', [
            'post' => $row['uid'],
            'day' => $date->format('d'),
            'month' => $date->format('m'),
            'year' => $date->format('Y'),
        ]);

        $conf = [
            'additionalParams' => $linkParameters,
            'forceAbsoluteUrl' => 1,
            'parameter' => $this->singlePid,
            'returnLast' => 'url',
            'useCacheHash' => true,
        ];
        $link = htmlspecialchars($this->cObj->typoLink('', $conf));

        return $link;
    }
}
