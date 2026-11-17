<?php

namespace FelixNagel\T3extblog\Utility;

/**
 * This file is part of the "t3extblog" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use FelixNagel\T3extblog\Exception\AccessDeniedException;
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Authentication\BackendUserAuthentication;
use TYPO3\CMS\Core\Type\Bitmask\Permission;

/**
 * Permission checks for the current backend user (in backend and frontend context).
 */
class PermissionUtility
{
    public const ACCESS_DENIED_HINT = 'Please contact your administrator to get the needed permissions.';
    /**
     * Check if the current backend user is allowed to see the given page (web mounts and page permissions).
     */
    public static function hasPageAccess(int $pid): bool
    {
        return static::getPage($pid) !== false;
    }

    public static function getPage(int $pid): array|false
    {
        $backendUser = static::getBackendUser();

        if ($backendUser === null || $pid <= 0) {
            return false;
        }

        return BackendUtility::readPageAccess($pid, $backendUser->getPagePermsClause(Permission::PAGE_SHOW));
    }

    /**
     * Check if the current backend user is allowed to read the given table.
     */
    public static function hasTableReadAccess(string $table): bool
    {
        return static::hasTableAccess($table);
    }

    /**
     * Check if the current backend user is allowed to modify the given table.
     */
    public static function hasTableModifyAccess(string $table): bool
    {
        return static::hasTableAccess($table, 'modify');
    }

    protected static function hasTableAccess(string $table, string $access = 'select'): bool
    {
        $backendUser = static::getBackendUser();

        return $backendUser !== null && $backendUser->check('tables_'.$access, $table);
    }

    public static function assertPageAccess(int $pid): void
    {
        if (!static::hasPageAccess($pid)) {
            throw new AccessDeniedException(
                'Access to page ' . $pid . ' denied. ' . self::ACCESS_DENIED_HINT,
                1794816001
            );
        }
    }

    /**
     * @param string[] $tables
     */
    public static function assertTableAccess(array $tables, bool $modify = false): void
    {
        foreach ($tables as $table) {
            if (!static::hasTableAccess($table, $modify ? 'modify' : 'select')) {
                throw new AccessDeniedException(
                    'Access to table ' . $table . ' denied. ' . self::ACCESS_DENIED_HINT,
                    1794816003
                );
            }
        }
    }

    /**
     * Filter a list of page UIDs or page records (with "uid" key) by page access.
     */
    public static function filterAccessiblePages(array $pages): array
    {
        return array_values(array_filter(
            $pages,
            static fn (mixed $page): bool => static::hasPageAccess((int)(is_array($page) ? $page['uid'] : $page))
        ));
    }

    protected static function getBackendUser(): ?BackendUserAuthentication
    {
        return $GLOBALS['BE_USER'] ?? null;
    }
}
