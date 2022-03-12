<?php

namespace FelixNagel\T3extblog\UpgradeWizard;

/**
 * This file is part of the "t3extblog" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use Symfony\Component\Console\Output\OutputInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Install\Updates\ChattyInterface;
use TYPO3\CMS\Install\Updates\Confirmation;
use TYPO3\CMS\Install\Updates\DatabaseUpdatedPrerequisite;
use TYPO3\CMS\Install\Updates\UpgradeWizardInterface;

/**
 * Abstract upgrade wizard for extension t3extblog.
 */
abstract class AbstractUpgradeWizard implements UpgradeWizardInterface, ChattyInterface
{
    /**
     * The database connection.
     */
    protected ConnectionPool $connectionPool;

    /**
     * @var OutputInterface
     */
    protected $output;

    public function __construct()
    {
        $this->connectionPool =  GeneralUtility::makeInstance(
            ConnectionPool::class
        );
    }

    public function getIdentifier(): string
    {
        return static::class;
    }

    public function getDescription(): string {
        return $this->getTitle();
    }

    public function updateNecessary(): bool {
        return true;
    }

    public function getConfirmation(): Confirmation {
        return new Confirmation(
            'Are you sure?',
            'This wizard will alter the database. Be careful in production environments!',
            false
        );
    }

    public function getPrerequisites(): array
    {
        return [
            DatabaseUpdatedPrerequisite::class,
        ];
    }

    public function setOutput(OutputInterface $output): void
    {
        $this->output = $output;
    }

    /**
     * Check if a tale field is available.
     */
    protected function isFieldAvailable(string $table, string $field): bool
    {
        return array_key_exists(
            $field,
            $this->connectionPool->getConnectionForTable($table)->getSchemaManager()->listTableColumns($table)
        );
    }
}
