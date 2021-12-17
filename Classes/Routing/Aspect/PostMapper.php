<?php
declare(strict_types = 1);

namespace FelixNagel\T3extblog\Routing\Aspect;

/**
 * This file is part of the "t3extblog" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Information\Typo3Version;

/**
 * PostMapper
 */
class PostMapper extends AbstractPersistedAliasMapper
{
    protected string $datePrefix;
    protected string $datePrefixRegex;
    protected bool $datePrefixLowercase = false;
    protected string $dateFieldName;

    public function __construct(array $settings)
    {
        // Set defaults
        $settings['tableName'] ??= 'tx_t3blog_post';
        $settings['routeFieldName'] ??= 'url_segment';

        $dateFieldName = $settings['dateFieldName'] ?? 'date';
        $datePrefix = $settings['datePrefix'] ?? null;
        $datePrefixRegex = $settings['datePrefixRegex'] ?? null;

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

            if (!is_string($datePrefixRegex)) {
                throw new \InvalidArgumentException('datePrefixRegex must be string', 1611742603);
            }
        }

        $this->dateFieldName = $dateFieldName;
        $this->datePrefix = $datePrefix;
        $this->datePrefixRegex = $datePrefixRegex;
        $this->datePrefixLowercase = (bool) ($settings['datePrefixLowercase'] ?? false);

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

        $result = $this->findByIdentifier($value);
        $result = $this->resolveOverlay($result);

        if (!isset($result[$this->routeFieldName])) {
            return null;
        }

        return $this->purgeRouteValuePrefix(
            $this->getRouteValueFromResult($result, (string)$result[$this->routeFieldName])
        );
    }

    public function resolve(string $value): ?string
    {
        if ($this->datePrefix === null) {
            return parent::resolve($value);
        }

        $valueBackup = $value;

        // Remove date prefix if existing
        $value = \preg_replace($this->datePrefixRegex, '', $value);

        if (!is_string($value)) {
            $this->logNotFound('Invalid blog post date given!');
            return null;
        }

        // Remove possible appended route string (e.g. "/comment")
        // Needed since TYPO3 9.5.15 and https://forge.typ  o3.org/issues/88291
        // @todo TYPO3 11 Test if this is still needed, if yes: remove condition, if no: remove
        if (version_compare(GeneralUtility::makeInstance(Typo3Version::class)->getVersion(), '9.5.15', '>=')) {
            $valueSplit = explode("/", $value);
            if ($valueSplit > 0) {
                $value =  $valueSplit[0];
            }
        }

        $value = $this->routeValuePrefix . $this->purgeRouteValuePrefix($value);
        $result = $this->findByRouteFieldValue($value);

        if ($result === null) {
            $this->logNotFound('Invalid blog post slug given!');
            return null;
        }

        // Comment submitted?
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

    protected function getRouteValueFromResult(array $result, string $value): string
    {
        $date = new \DateTime(date('c', (int)$result[$this->dateFieldName]));

        if ($date instanceof \DateTime) {
            $formattedDate = $date->format($this->datePrefix);

            if ($this->datePrefixLowercase) {
                $formattedDate = strtolower($formattedDate);
            }

            $value = $formattedDate.$value;
        }

        return $value;
    }

    protected function buildPersistenceFieldNames(): array
    {
        $fields = parent::buildPersistenceFieldNames();
        $fields[] = $this->dateFieldName;

        return $fields;
    }
}
