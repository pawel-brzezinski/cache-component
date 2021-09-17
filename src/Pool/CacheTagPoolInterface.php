<?php

declare(strict_types=1);

namespace PB\Component\Cache\Pool;

/**
 * @author Paweł Brzeziński <pawel.brzezinski@smartint.pl>
 */
interface CacheTagPoolInterface extends CachePoolInterface
{
    /**
     * @param string[] $tags
     *
     * @return bool
     */
    public function invalidateTags(array $tags): bool;
}
