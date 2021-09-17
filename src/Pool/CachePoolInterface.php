<?php

declare(strict_types=1);

namespace PB\Component\Cache\Pool;

use Psr\Cache\InvalidArgumentException;

/**
 * @author Paweł Brzeziński <pawel.brzezinski@smartint.pl>
 */
interface CachePoolInterface
{
    /**
     * @param string $key
     * @param callable $callback
     *
     * @return mixed
     *
     * @throws InvalidArgumentException
     *
     * @phpstan-ignore-next-line
     */
    public function get(string $key, callable $callback);

    /**
     * @param string $key
     *
     * @return bool
     *
     * @throws InvalidArgumentException
     *
     * @phpstan-ignore-next-line
     */
    public function delete(string $key): bool;
}
