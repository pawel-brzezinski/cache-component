<?php

declare(strict_types=1);

namespace PB\Component\Cache\Pool;

use Symfony\Component\Cache\Adapter\RedisAdapter;

/**
 * @author Paweł Brzeziński <pawel.brzezinski@smartint.pl>
 */
class RedisCachePool implements CachePoolInterface
{
    private RedisAdapter $adapter;

    /**
     * @param string $redisDsn
     */
    public function __construct(string $redisDsn)
    {
        $this->createAdapter($redisDsn);
    }

    /**
     * {@inheritDoc}
     */
    public function get(string $key, callable $callback)
    {
        return $this->adapter->get($key, $callback);
    }

    /**
     * {@inheritDoc}
     */
    public function delete(string $key): bool
    {
        return $this->adapter->delete($key);
    }

    /**
     * @param string $dsn
     */
    protected function createAdapter(string $dsn): void
    {
        $connection = RedisAdapter::createConnection($dsn);
        $this->adapter = new RedisAdapter($connection);
    }
}
