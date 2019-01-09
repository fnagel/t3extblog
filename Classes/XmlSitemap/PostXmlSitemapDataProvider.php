<?php

namespace FelixNagel\T3extblog\XmlSitemap;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2018 Felix Nagel <info@felixnagel.com>
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
