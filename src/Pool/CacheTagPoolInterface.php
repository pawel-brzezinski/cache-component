<?php

declare(strict_types=1);

namespace PB\Component\Cache\Pool;

use Psr\Cache\InvalidArgumentException;

/**
 * @author Paweł Brzeziński <pawel.brzezinski@smartint.pl>
 */
interface CacheTagPoolInterface extends CachePoolInterface
{
    /**
     * @param string[] $tags
     *
     * @return bool
     *
     * @throws InvalidArgumentException
     */
    public function invalidateTags(array $tags): bool;
}
