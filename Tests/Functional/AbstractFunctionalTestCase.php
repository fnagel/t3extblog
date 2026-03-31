<?php

declare(strict_types=1);

namespace FelixNagel\T3extblog\Tests\Functional;

/*
 * This file is part of the "t3extblog" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;

abstract class AbstractFunctionalTestCase extends FunctionalTestCase
{
    protected array $testExtensionsToLoad = [
        'typo3conf/ext/t3extblog',
    ];

    protected array $coreExtensionsToLoad = [
        'dashboard',
    ];
}
