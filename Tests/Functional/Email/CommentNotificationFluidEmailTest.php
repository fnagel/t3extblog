<?php

declare(strict_types=1);

namespace FelixNagel\T3extblog\Tests\Functional\Email;

/*
 * This file is part of the "t3extblog" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

/**
 * Runs the exact same comment notification email assertions as
 * {@see CommentNotificationEmailTest}, but with the email type switched to
 * FluidEmail instead of the default MailMessage. This ensures both supported
 * mail implementations (Mail/FluidEmail and Mail/MailMessage) send correct mails.
 */
final class CommentNotificationFluidEmailTest extends CommentNotificationEmailTest
{
    protected function additionalTypoScript(): array
    {
        return [
            'plugin.tx_t3extblog.email.type = fluidEmail',
        ];
    }
}
