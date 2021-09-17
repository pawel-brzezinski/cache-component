<?php

declare(strict_types=1);

namespace PB\Component\Cache\Tests\Pool;

use PB\Component\Cache\Pool\RedisCachePool;
use PB\Component\Cache\Tests\TestCase\RedisTestCase;
use PB\Component\CQRS\Tests\Assertions\AssertObjectMethod;
use Psr\Cache\InvalidArgumentException;
use ReflectionException;

/**
 * @author Paweł Brzeziński <pawel.brzezinski@smartint.pl>
 */
final class RedisCachePoolTest extends RedisTestCase
{
    use AssertObjectMethod;

    #########################
    # RedisCachePool::get() #
    #########################

    /**
     * @throws InvalidArgumentException
     *
     * @phpstan-ignore-next-line
     */
    public function testShouldCallGetMethodAndCheckIfSymfonyCacheGetMethodHasBeenCalled(): void
    {
        // Given
        $cachePoolUnderTest = $this->createCachePool();

        $key = 'foo';
        $value = 'bar';

        $cacheNotFoundCount = 0;
        $getCallable = function() use ($value, &$cacheNotFoundCount) {
            $cacheNotFoundCount++;

            return $value;
        };

        // When & Then
        $actual = $cachePoolUnderTest->get($key, $getCallable);
        $this->assertSame($value, $actual);
        $this->assertSame(1, $cacheNotFoundCount);

        // Check whether in next call value will be fetched from Redis.
        $actual = $cachePoolUnderTest->get($key, $getCallable);
        $this->assertSame($value, $actual);
        $this->assertSame(1, $cacheNotFoundCount);
    }

    #######
    # End #
    #######

    ############################
    # RedisCachePool::delete() #
    ############################

    /**
     * @throws InvalidArgumentException
     *
     * @phpstan-ignore-next-line
     */
    public function testShouldCallDeleteMethodAndCheckIfSymfonyCacheDeleteMethodHasBeenCalled(): void
    {
        // Given
        $cachePoolUnderTest = $this->createCachePool();

        $key = 'foo';
        $value = 'bar';

        $testConnection = $this->createRedisConnection();
        $testConnection->set($key, $value);

        // When & Then
        $this->assertSame($value, $testConnection->get($key));

        $actual = $cachePoolUnderTest->delete($key);
        $this->assertTrue($actual);

        $this->assertFalse($testConnection->get($key));
    }

    #######
    # End #
    #######

    ###################################
    # RedisCachePool::createAdapter() #
    ###################################

    /**
     * @throws ReflectionException
     */
    public function testShouldCheckIfCreateAdapterMethodIsProtected(): void
    {
        // When & Then
        $this->assertMethodIsProtected(RedisCachePool::class, 'createAdapter');
    }

    #######
    # End #
    #######

    /**
     * @return RedisCachePool
     */
    private function createCachePool(): RedisCachePool
    {
        return new RedisCachePool($_ENV['REDIS_DSN']);
    }
}
