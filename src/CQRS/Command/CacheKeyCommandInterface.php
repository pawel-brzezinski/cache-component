<?php

declare(strict_types=1);

namespace PB\Component\Cache\CQRS\Command;

use PB\Component\CQRS\Command\CommandInterface;

/**
 * Interface for cache invalidate by key command message implementation.
 *
 * @author Paweł Brzeziński <pawel.brzezinski@smartint.pl>
 */
interface CacheKeyCommandInterface extends CommandInterface
{
    /**
     * Returns cache key.
     *
     * @return string
     */
    public function cacheKey(): string;
}
