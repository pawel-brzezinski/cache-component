<?php

declare(strict_types=1);

namespace PB\Component\Cache\CQRS\Command;

use PB\Component\CQRS\Command\CommandInterface;

/**
 * Interface for cache invalidate by tags command message implementation.
 *
 * @author Paweł Brzeziński <pawel.brzezinski@smartint.pl>
 */
interface CacheTagCommandInterface extends CommandInterface
{
    /**
     * Returns custom cache tags. Supported by TagAwareCacheInterface adapters.
     *
     * @return string[]
     */
    public function cacheTags(): array;
}
