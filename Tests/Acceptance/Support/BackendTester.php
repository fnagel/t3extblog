<?php

declare(strict_types=1);

namespace FelixNagel\T3extblog\Tests\Acceptance\Support;

/*
 * This file is part of the "t3extblog" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

/**
 * Backend acceptance tester.
 *
 * Generated actions are placed in _generated/BackendTesterActions.php.
 * Run `.Build/bin/codecept build -c Tests/codeception.yml` to regenerate.
 *
 * @method void wantTo($text)
 * @method void wantToTest($text)
 * @method void execute($callable)
 * @method void expectTo($prediction)
 * @method void expect($prediction)
 * @method void amGoingTo($argumentation)
 * @method void am($role)
 * @method void lookForwardTo($achieveValue)
 * @method void comment($description)
 */
class BackendTester extends \Codeception\Actor
{
    use _generated\BackendTesterActions;
    use \TYPO3\TestingFramework\Core\Acceptance\Step\FrameSteps;
}
