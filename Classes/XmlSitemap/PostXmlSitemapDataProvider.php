<?php

namespace FelixNagel\T3extblog\XmlSitemap;

/**
 * This file is part of the "t3extblog" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use TYPO3\CMS\Seo\XmlSitemap\RecordsXmlSitemapDataProvider;

/**
 * PostXmlSitemapDataProvider
 */
class PostXmlSitemapDataProvider extends RecordsXmlSitemapDataProvider
{
    /**
     * @inheritdoc
     */
    protected function getUrlFieldParameterMap(array $additionalParams, array $data): array
    {
        $additionalParams = parent::getUrlFieldParameterMap($additionalParams, $data);

        if (isset($this->config['url']['addDateFieldsToParameterMap']) &&
            $this->config['url']['addDateFieldsToParameterMap']
        ) {
            $date = new \DateTime('@'.$data['date']);
            $additionalParams['tx_t3extblog_blogsystem[year]'] = $date->format('Y');
            $additionalParams['tx_t3extblog_blogsystem[month]'] = $date->format('m');
            $additionalParams['tx_t3extblog_blogsystem[day]'] = $date->format('d');
        }

        return $additionalParams;
    }
}
