<?php
declare(strict_types = 1);

namespace FelixNagel\T3extblog\Routing\Aspect;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2019 Felix Nagel <info@felixnagel.com>
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

use TYPO3\CMS\Core\Routing\Aspect\PersistedAliasMapper;
use TYPO3\CMS\Core\Site\SiteLanguageAwareTrait;

/**
 * PostMapper
 */
class PostMapper extends PersistedAliasMapper
{
    use SiteLanguageAwareTrait;

    /**
     * @var string
     */
    protected $datePrefix;

    /**
     * @var string
     */
    protected $dateFieldName;

    /**
     * @param array $settings
     * @throws \InvalidArgumentException
     */
    public function __construct(array $settings)
    {
        // Set defaults
        $settings['tableName'] = $settings['tableName'] ?? 'tx_t3blog_post';
        $settings['routeFieldName'] = $settings['routeFieldName'] ?? 'url_segment';

        $dateFieldName = $settings['dateFieldName'] ?? 'date';
        $datePrefix = $settings['datePrefix'] ?? null;

        if (!is_string($dateFieldName)) {
            throw new \InvalidArgumentException('dateFieldName must be string', 1537277135);
        }

        if ($datePrefix !== null) {
            if (!is_string($datePrefix)) {
                throw new \InvalidArgumentException('datePrefix must be string', 1537277134);
            }
            $date = new \DateTime();
            if (empty($date->format($datePrefix))) {
                throw new \InvalidArgumentException('datePrefix must be valid DateTime value', 1550748751);
            }
        }

        $this->dateFieldName = $dateFieldName;
        $this->datePrefix = $datePrefix;

        parent::__construct($settings);
    }

    /**
     * {@inheritdoc}
     */
    public function generate(string $value): ?string
    {
        if ($this->datePrefix === null) {
            return parent::generate($value);
        }

        $result = $this->getPersistenceDelegate()->generate([
            'uid' => $value
        ]);
        $result = $this->resolveOverlay($result);

        if (!isset($result[$this->routeFieldName])) {
            return null;
        }

        $value = (string)$result[$this->routeFieldName];
        $value = $this->getRouteValueFromResult($result, $value);

        return $this->purgeRouteValuePrefix($value);
    }

    /**
     * {@inheritdoc}
     */
    public function resolve(string $value): ?string
    {
        if ($this->datePrefix === null) {
            return parent::resolve($value);
        }

        $valueBackup = $value;
        $date = new \DateTime();
        $value = substr($value, strlen($date->format($this->datePrefix)));

        $value = $this->routeValuePrefix . $this->purgeRouteValuePrefix($value);
        $result = $this->getPersistenceDelegate()->resolve([
            $this->routeFieldName => $value
        ]);

        $value = $this->getRouteValueFromResult($result, $value);
        if ($valueBackup !== $value) {
            return null;
        }

        if ($result[$this->languageParentFieldName] ?? null > 0) {
            return (string)$result[$this->languageParentFieldName];
        }

        if (isset($result['uid'])) {
            return (string)$result['uid'];
        }

        return null;
    }

    /**
     * @param array $result
     * @param string $value
     * @return string
     */
    protected function getRouteValueFromResult(array $result, $value)
    {
        $date = new \DateTime(date('c', (int)$result[$this->dateFieldName]));

        if ($date instanceof \DateTime) {
            $value = $date->format($this->datePrefix).$value;
        }

        return $value;
    }

    /**
     * @inheritDoc
     */
    protected function buildPersistenceFieldNames(): array
    {
        $fields = parent::buildPersistenceFieldNames();
        $fields[] = $this->dateFieldName;

        return $fields;
    }
}
