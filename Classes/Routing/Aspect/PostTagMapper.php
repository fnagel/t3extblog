<?php
declare(strict_types = 1);

namespace FelixNagel\T3extblog\Routing\Aspect;

/**
 * This file is part of the "t3extblog" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use TYPO3\CMS\Core\Database\Query\QueryBuilder;

/**
 * PostTagMapper
 */
class PostTagMapper extends AbstractPersistedAliasMapper
{
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
