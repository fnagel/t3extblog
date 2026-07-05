<?php

declare(strict_types=1);

namespace FelixNagel\T3extblog\Tests\Functional\Email;

/*
 * This file is part of the "t3extblog" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use FelixNagel\T3extblog\Tests\Functional\Controller\AbstractControllerTestCase;
use Symfony\Component\Mailer\SentMessage;
use Symfony\Component\Mime\Email;

/**
 * Base class for tests that assert sent emails.
 *
 * Instead of a real transport (or the mailcatcher/mailpit setup that would
 * complicate CI) the mailer is configured for "spooling using files":
 * every message is serialized to a file inside the test instance instead of
 * being delivered. After triggering the code under test the spooled files are
 * read back and the resulting {@see Email} objects are asserted.
 *
 * @see https://docs.typo3.org/permalink/t3coreapi:confval-globals-typo3-conf-vars-mail-transport
 */
abstract class AbstractEmailTestCase extends AbstractControllerTestCase
{
    /**
     * Relative to the test instance root; resolved by GeneralUtility::getFileAbsFileName().
     */
    protected const SPOOL_PATH = 'typo3temp/var/spool';

    protected array $configurationToUseInTestInstance = [
        'MAIL' => [
            // The real transport is never used while spooling to files, but a
            // valid value is required for the mailer to be built.
            'transport' => 'sendmail',
            'transport_spool_type' => 'file',
            'transport_spool_filepath' => self::SPOOL_PATH,
        ],
        'FE' => [
            'pageNotFoundOnCHashError' => false,
            'cacheHash' => [
                'enforceValidation' => false,
            ],
        ],
    ];

    protected function setUp(): void
    {
        parent::setUp();

        // Start each test with an empty spool directory.
        $this->clearSpool();
    }

    /**
     * Absolute path of the mail spool directory inside the test instance.
     */
    protected function getSpoolPath(): string
    {
        return $this->instancePath . '/' . self::SPOOL_PATH;
    }

    /**
     * Remove all spooled messages.
     */
    protected function clearSpool(): void
    {
        $path = $this->getSpoolPath();

        if (!is_dir($path)) {
            return;
        }

        foreach ((array)glob($path . '/*') as $file) {
            if (is_file($file)) {
                unlink($file);
            }
        }
    }

    /**
     * Read and deserialize all spooled messages.
     *
     * @return SentMessage[]
     */
    protected function getSpooledMessages(): array
    {
        $messages = [];

        foreach ((array)glob($this->getSpoolPath() . '/*.message') as $file) {
            $message = unserialize((string)file_get_contents($file));

            if ($message instanceof SentMessage) {
                $messages[] = $message;
            }
        }

        return $messages;
    }

    /**
     * Read all spooled messages as their original {@see Email} objects.
     *
     * @return Email[]
     */
    protected function getSpooledEmails(): array
    {
        $emails = [];

        foreach ($this->getSpooledMessages() as $message) {
            $original = $message->getOriginalMessage();

            if ($original instanceof Email) {
                $emails[] = $original;
            }
        }

        return $emails;
    }

    /**
     * Find the first spooled email addressed to the given recipient.
     */
    protected function findEmailTo(string $address): ?Email
    {
        foreach ($this->getSpooledEmails() as $email) {
            foreach ($email->getTo() as $to) {
                if (strcasecmp($to->getAddress(), $address) === 0) {
                    return $email;
                }
            }
        }

        return null;
    }
}
