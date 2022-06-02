<?php

namespace FelixNagel\T3extblog\Service;

/**
 * This file is part of the "t3extblog" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\SingletonInterface;

/**
 * RateLimiterServiceInterface.
 */
interface RateLimiterServiceInterface extends SingletonInterface
{
    public function create(ServerRequestInterface $request, string $key, array $settings): self;

    public function consume(string $key): self;

    public function isAccepted(string $key): bool;

    public function reset(string $key): self;
}
