<?php

declare(strict_types=1);

namespace PB\Component\Cache\Value;

/**
 * Interface for cacheable and serializable object implementation.
 *
 * @author Paweł Brzeziński <pawel.brzezinski@smartint.pl>
 */
interface SerializableCacheValueInterface
{
    /**
     * Returns object instance from serialized cache value.
     *
     * @param array $data
     *
     * @return object
     */
    public static function deserializeFromCacheValue(array $data): object;

    /**
     * Returns object data serialized to cache value array.
     *
     * @return array
     */
    public function serializeToCacheValue(): array;
}
