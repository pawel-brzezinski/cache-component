<?php

declare(strict_types=1);

namespace PB\Component\Cache\Pool;

use Symfony\Component\Cache\Adapter\{RedisAdapter, RedisTagAwareAdapter};

/**
 * @author Paweł Brzeziński <pawel.brzezinski@smartint.pl>
 */
class RedisCacheTagPool extends RedisCachePool implements CacheTagPoolInterface
{
    private RedisTagAwareAdapter $adapter;

    /**
     * {@inheritDoc}
     */
    public function invalidateTags(array $tags): bool
    {
        return $this->adapter->invalidateTags($tags);
    }

    /**
     * @param string $dsn
     */
    protected function createAdapter(string $dsn): void
    {
        $connection = RedisAdapter::createConnection($dsn);
        $this->adapter = new RedisTagAwareAdapter($connection);
    }
}
