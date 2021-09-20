<?php

declare(strict_types=1);

namespace PB\Component\Cache\Exception;

use Exception;

/**
 * @author Paweł Brzeziński <pawel.brzezinski@smartint.pl>
 */
final class NotFoundInCacheException extends Exception
{
    /**
     * NotFoundInCacheException constructor.
     *
     * @param string $cacheKey
     */
    public function __construct(string $cacheKey)
    {
        parent::__construct(sprintf('Key "%s" not found in cache.', $cacheKey));
    }
}
