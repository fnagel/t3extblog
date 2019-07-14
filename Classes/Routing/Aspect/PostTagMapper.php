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

use TYPO3\CMS\Core\Database\Query\QueryBuilder;
use TYPO3\CMS\Core\Routing\Aspect\PersistedAliasMapper;
use TYPO3\CMS\Core\Site\SiteLanguageAwareTrait;

/**
 * PostTagMapper
 */
class PostTagMapper extends PersistedAliasMapper
{
    use SiteLanguageAwareTrait;

    /**
     * @var bool
     */
    protected $isFieldNameCsv = true;

    /**
     * @param array $settings
     * @throws \InvalidArgumentException
     */
    public function __construct(array $settings)
    {
        // Set defaults
        $settings['tableName'] = $settings['tableName'] ?? 'tx_t3blog_post';
        $settings['routeFieldName'] = $settings['routeFieldName'] ?? 'tagClouds';

        parent::__construct($settings);
    }

    /**
     * {@inheritdoc}
     */
    public function generate(string $value): ?string
    {
        $result = $this->getPersistenceDelegate()->exists([
            $this->routeFieldName => $value
        ]);

        if ($result === false) {
            return null;
        }

        return $this->purgeRouteValuePrefix($value);
    }

    /**
     * {@inheritdoc}
     */
    public function resolve(string $value): ?string
    {
        $value = $this->routeValuePrefix . $this->purgeRouteValuePrefix($value);

        $result = $this->getPersistenceDelegate()->resolve([
            $this->routeFieldName => $value
        ]);

        if ($result === false) {
            return null;
        }

        return $value;
    }


    /**
     * @param QueryBuilder $queryBuilder
     * @param array $values
     * @return array
     */
    protected function createFieldConstraints(QueryBuilder $queryBuilder, array $values): array
    {
        if (!$this->isFieldNameCsv) {
            return parent::createFieldConstraints($queryBuilder, $values);
        }

        $constraints = [];

        foreach ($values as $fieldName => $fieldValue) {
            $constraints[] = $queryBuilder->expr()->in(
                $fieldName,
                $queryBuilder->createNamedParameter(
                    $fieldValue,
                    \PDO::PARAM_STR
                )
            );

        }

        return $constraints;
    }
}
