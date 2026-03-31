<?php

declare(strict_types=1);

namespace FelixNagel\T3extblog\Tests\Unit\Validation\Validator;

use FelixNagel\T3extblog\Service\SettingsService;
use FelixNagel\T3extblog\Validation\Validator\PrivacyPolicyValidator;
use PHPUnit\Framework\Attributes\CoversClass;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

#[CoversClass(PrivacyPolicyValidator::class)]
class PrivacyPolicyValidatorTest extends UnitTestCase
{
    protected bool $resetSingletonInstances = true;

    protected PrivacyPolicyValidator $subject;

    protected function setUp(): void
    {
        parent::setUp();
        $this->subject = new PrivacyPolicyValidator();
        $this->subject->setOptions(['key' => 'blog', 'property' => null]);
    }

    protected function addSettingsServiceMock(bool $privacyPolicyEnabled): void
    {
        $settingsService = $this->getMockBuilder(SettingsService::class)
            ->disableOriginalConstructor()
            ->getMock();

        $settingsService->method('getTypoScriptSettings')
            ->willReturn([
                'blogSubscription' => [
                    'privacyPolicy' => [
                        'enabled' => $privacyPolicyEnabled,
                    ],
                ],
                'blogsystem' => [
                    'comments' => [
                        'privacyPolicy' => [
                            'enabled' => $privacyPolicyEnabled,
                        ],
                    ],
                ],
            ]);

        GeneralUtility::addInstance(SettingsService::class, $settingsService);
    }

    public function testAcceptedPrivacyPolicyPassesValidation(): void
    {
        $this->addSettingsServiceMock(true);

        $result = $this->subject->validate(true);

        self::assertFalse($result->hasErrors());
    }

    public function testNotAcceptedPrivacyPolicyFailsValidationWhenEnabled(): void
    {
        $this->addSettingsServiceMock(true);

        $result = $this->subject->validate(false);

        self::assertTrue($result->hasErrors());
    }

    public function testNotAcceptedPrivacyPolicyPassesWhenDisabled(): void
    {
        $this->addSettingsServiceMock(false);

        $result = $this->subject->validate(false);

        self::assertFalse($result->hasErrors());
    }
}
