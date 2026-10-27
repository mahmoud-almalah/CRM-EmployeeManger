<?php

declare(strict_types=1);

namespace Infrastructure\Health;

use Application\Health\Contracts\HealthProbeContract;
use Illuminate\Cache\CacheManager;
use RuntimeException;

final class CacheProbe implements HealthProbeContract
{
    public function __construct(private readonly CacheManager $cache) {}

    public function name(): string
    {
        return 'cache';
    }

    public function check(): void
    {
        $store = $this->cache->store();

        $store->put('health-check', 'ok', 10);

        if ($store->get('health-check') !== 'ok') {
            throw new RuntimeException('The cache did not return the value that was just written.');
        }
    }
}
