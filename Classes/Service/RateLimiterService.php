<?php

namespace FelixNagel\T3extblog\Service;

/**
 * This file is part of the "t3extblog" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use FelixNagel\T3extblog\Exception\Exception;
use FelixNagel\T3extblog\Traits\LoggingTrait;
use Psr\Http\Message\ServerRequestInterface;
use Symfony\Component\RateLimiter\LimiterInterface;
use Symfony\Component\RateLimiter\RateLimit;
use Symfony\Component\RateLimiter\RateLimiterFactory as Factory;
use Symfony\Component\RateLimiter\Storage\InMemoryStorage;
use TYPO3\CMS\Core\Http\NormalizedParams;
use TYPO3\CMS\Core\RateLimiter\Storage\CachingFrameworkStorage;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * RateLimiterService.
 *
 * Partly taken from \TYPO3\CMS\Core\RateLimiter\RateLimiterFactory
 */
class RateLimiterService implements RateLimiterServiceInterface
{
    use LoggingTrait;

    /**
     * @var LimiterInterface[]
     */
    protected array $limiter = [];

    /**
     * @var RateLimit[]
     */
    protected array $rateLimits = [];

    protected ?string $ipAddress = null;

    public function create(ServerRequestInterface $request, string $key, array $settings): self
    {
        if ($this->ipAddress === null) {
            $attributes = $request->getAttribute('normalizedParams') ?? NormalizedParams::createFromRequest($request);
            $this->ipAddress = $attributes->getRemoteAddress();
        }

        if (!array_key_exists($key, $this->limiter)) {
            $this->limiter[$key] = $this->createLimiter($this->ipAddress, $key, $settings);
        }

        return $this;
    }

    protected function getLimiter(string $key): LimiterInterface
    {
        if (!array_key_exists($key, $this->limiter)) {
            throw new Exception('Unknown limiter!');
        }

        return $this->limiter[$key];
    }

    protected function getRateLimit(string $key): RateLimit
    {
        if (!array_key_exists($key, $this->rateLimits)) {
            throw new Exception('Unknown rate limit!');
        }

        return $this->rateLimits[$key];
    }

    public function consume(string $key): self
    {
        $this->rateLimits[$key] = $this->getLimiter($key)->consume();

        return $this;
    }

    public function isAccepted(string $key): bool
    {
        if (!$this->getRateLimit($key)->isAccepted()) {
            $this->log->dev('POST request (context is {key}) has been rate limited for IP address {ipAddress}', [
                'ipAddress' => $this->ipAddress,
                'key' => $key,
            ]);
            return false;
        }

        return true;
    }

    public function reset(string $key): self
    {
        if (array_key_exists($key, $this->limiter) && $this->limiter[$key] instanceof LimiterInterface) {
            $this->limiter[$key]->reset();
        }

        return $this;
    }

    protected function createLimiter(string $remoteIp, string $key, array $settings): LimiterInterface
    {
        $limiterId = sha1('t3extblog-' . $key);
        $limit = (int)($settings['limit'] ?? 5);
        $interval = $settings['interval'] ?? '15 minutes';
        $enabled = !$this->isIpExcluded($settings, $remoteIp) && $limit > 0;

        $config = [
            'id' => $limiterId,
            'policy' => ($enabled ? 'sliding_window' : 'no_limit'),
            'limit' => $limit,
            'interval' => $interval,
        ];
        $storage = ($enabled ? GeneralUtility::makeInstance(CachingFrameworkStorage::class) : new InMemoryStorage());

        return (new Factory($config, $storage))->create($remoteIp);
    }

    protected function isIpExcluded(array $settings, string $remoteAddress): bool
    {
        return GeneralUtility::cmpIP($remoteAddress, trim($settings['ipExcludeList'] ?? ''));
    }
}
