<?php

declare(strict_types=1);

namespace FelixNagel\T3extblog\Tests\Functional\Domain\Repository;

/*
 * This file is part of the "t3extblog" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use FelixNagel\T3extblog\Domain\Repository\CommentRepository;
use FelixNagel\T3extblog\Tests\Functional\AbstractFunctionalTestCase;
use PHPUnit\Framework\Attributes\Test;
use TYPO3\CMS\Core\Core\SystemEnvironmentBuilder;
use TYPO3\CMS\Core\Http\ServerRequest;
use TYPO3\CMS\Core\TypoScript\AST\Node\RootNode;
use TYPO3\CMS\Core\TypoScript\FrontendTypoScript;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;

/**
 * Validates that AbstractRepository::createQuery() applies the configured storage PID.
 *
 * When a frontend request is handled before the Extbase plugin configuration has been
 * initialized (as happens with PAGEVIEW based page rendering), the underlying
 * QueryFactory's first configuration lookup receives an empty inline configuration and
 * therefore resolves an empty storage PID ([0]). AbstractRepository::createQuery() must
 * detect this and correct the query settings from the TypoScript storagePid.
 */
final class RepositoryStoragePidTest extends AbstractFunctionalTestCase
{
    protected array $configurationToUseInTestInstance = [
        'MAIL' => [
            'transport' => 'null',
        ],
        'FE' => [
            'pageNotFoundOnCHashError' => false,
            'cacheHash' => [
                'enforceValidation' => false,
            ],
        ],
    ];

    #[Test]
    public function createQueryAppliesConfiguredStoragePidWhenExtbaseDefaultsToZero(): void
    {
        $this->provideFrontendRequest();

        $query = $this->get(CommentRepository::class)->createQuery();

        self::assertSame([400], $query->getQuerySettings()->getStoragePageIds());
    }

    /**
     * Simulates a frontend request whose inline Extbase configuration is still empty
     * (the state before the plugin Bootstrap has called setConfiguration()), but which
     * already carries the compiled frontend TypoScript setup. This reproduces the
     * storagePid=[0] scenario AbstractRepository::createQuery() has to handle.
     */
    private function provideFrontendRequest(): void
    {
        $frontendTypoScript = new FrontendTypoScript(new RootNode(), [], [], []);
        $frontendTypoScript->setSetupArray([
            'plugin.' => [
                'tx_t3extblog.' => [
                    'persistence.' => [
                        'storagePid' => '400',
                    ],
                ],
            ],
        ]);

        $request = (new ServerRequest('https://localhost/'))
            ->withAttribute('applicationType', SystemEnvironmentBuilder::REQUESTTYPE_FE)
            ->withAttribute('frontend.typoscript', $frontendTypoScript);

        $GLOBALS['TYPO3_REQUEST'] = $request;
        $this->get(ConfigurationManagerInterface::class)->setRequest($request);
    }
}
