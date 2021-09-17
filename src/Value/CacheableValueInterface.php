<?php

declare(strict_types=1);

namespace PB\Component\Cache\Value;

/**
 * Interface for cacheable object implementation.
 *
 * @author Paweł Brzeziński <pawel.brzezinski@smartint.pl>
 */
interface CacheableValueInterface
{
    /**
     * Returns cache key.
     *
     * @return string
     */
    public function cacheKey(): string;

    /**
     * Returns cache lifetime.
     *
     * @return int
     */
    public function cacheLifetime(): int;

    /**
     * Returns custom cache tags. Supported by TagAwareCacheInterface adapters.
     *
     * @return string[]
     */
    public function cacheTags(): array;

    /**
     * Returns cache tag generator id. Supported by TagAwareCacheInterface adapters.
     *
     * @return string|null
     */
    public function cacheTagGeneratorId(): ?string;
}
