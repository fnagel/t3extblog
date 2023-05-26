<?php
declare(strict_types = 1);

namespace FelixNagel\T3extblog\Routing\Aspect;

/**
 * This file is part of the "t3extblog" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

/**
 * PostTagMapper
 */
class PostTagMapper extends AbstractPersistedAliasMapper
{
    public function __construct(array $settings)
    {
        // Set defaults
        $settings['tableName'] ??= 'tx_t3blog_post';
        $settings['routeFieldName'] ??= 'tagClouds';

        parent::__construct($settings);
    }

    /**
     * Check if tag is in any post tag cloud CSV field
     * Return plain value as the field may contain multiple tags
     */
    public function generate(string $value): ?string
    {
        $result = $this->findByIdentifier($value);
        $result = $this->resolveOverlay($result);

        if (!isset($result[$this->routeFieldName])) {
            return null;
        }

        return $this->purgeRouteValuePrefix($value);
    }

    /**
     * Check if tag is in any post tag cloud CSV field
     * Return given value when existing.
     */
    public function resolve(string $value): ?string
    {
        $value = $this->purgeRouteValuePrefix($value);

        if ($value && $this->generate($value)) {
            return $value;
        }

        return null;
    }

    /**
     * Search in post tag cloud field (CSV field)
     * Search with a space before and after due to FIND_IN_SET limitations
     */
    protected function findByIdentifier(string $value): ?array
    {
        $queryBuilder = $this->createQueryBuilder();
        $result = $queryBuilder
            ->select(...$this->persistenceFieldNames)
            ->where(
                $queryBuilder->expr()->or(
                    $queryBuilder->expr()->inSet($this->routeFieldName, $queryBuilder->quote($value)),
                    $queryBuilder->expr()->inSet($this->routeFieldName, $queryBuilder->quote(' '.$value)),
                    $queryBuilder->expr()->inSet($this->routeFieldName, $queryBuilder->quote($value.' '))
                )
            )
            ->executeQuery()
            ->fetchAssociative();

        return $result !== false ? $result : null;
    }
}
