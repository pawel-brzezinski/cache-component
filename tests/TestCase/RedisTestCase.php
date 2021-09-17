<?php

declare(strict_types=1);

namespace PB\Component\Cache\Tests\TestCase;

use Redis;
use Symfony\Component\Cache\Adapter\RedisAdapter;

/**
 * @author Paweł Brzeziński <pawel.brzezinski@smartint.pl>
 */
abstract class RedisTestCase extends ComponentTestCase
{
    protected Redis $redisConnection;

    /**
     * {@inheritDoc}
     */
    protected function setUp(): void
    {
        $this->redisConnection = $this->createRedisConnection();
        $this->redisConnection->flushDB();
    }

    /**
     * {@inheritDoc}
     */
    protected function tearDown(): void
    {
        $this->redisConnection->flushDB();
    }

    /**
     * @return Redis
     */
    protected function createRedisConnection(): Redis
    {
        return RedisAdapter::createConnection($_ENV['REDIS_DSN']);
    }
}
