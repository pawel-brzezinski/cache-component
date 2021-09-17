<?php

declare(strict_types=1);

namespace PB\Component\Cache\Tests\Pool;

use PB\Component\Cache\Pool\RedisCacheTagPool;
use PB\Component\Cache\Tests\TestCase\RedisTestCase;
use PB\Component\CQRS\Tests\Assertions\AssertObjectMethod;
use Psr\Cache\InvalidArgumentException;
use ReflectionException;
use Symfony\Component\Cache\Adapter\RedisTagAwareAdapter;
use Symfony\Component\Cache\CacheItem;

/**
 * @author Paweł Brzeziński <pawel.brzezinski@smartint.pl>
 */
final class RedisCacheTagPoolTest extends RedisTestCase
{
    use AssertObjectMethod;

    ####################################
    # RedisCachePool::invalidateTags() #
    ####################################

    /**
     * @throws InvalidArgumentException
     *
     * @phpstan-ignore-next-line
     */
    public function testShouldCallInvalidateTagsMethodAndCheckIfSymfonyCacheInvalidateTagsMethodHasBeenCalled(): void
    {
        // Given
        $cachePoolUnderTest = $this->createCachePool();

        $testConnection = $this->createRedisConnection();
        $testAdapter = new RedisTagAwareAdapter($testConnection);

        $key1 = 'foo';
        $value1 = 'bar';
        $key2 = 'lorem';
        $value2 = 'ipsum';
        $tags = ['tag-1', 'tag-2'];

        $testAdapter->get($key1, function (CacheItem $cacheItem) use ($tags, $value1) {
            $cacheItem->tag($tags);
            return $value1;
        });

        $testAdapter->get($key2, function (CacheItem $cacheItem) use ($tags, $value2) {
            $cacheItem->tag($tags);
            return $value2;
        });

        // When & Then
        $this->assertNotFalse($testConnection->get($key1));
        $this->assertNotFalse($testConnection->get($key2));

        $actual = $cachePoolUnderTest->invalidateTags(['tag-1']);
        $this->assertTrue($actual);

        $this->assertFalse($testConnection->get($key1));
        $this->assertFalse($testConnection->get($key2));
    }

    #######
    # End #
    #######

    /**
     * @throws ReflectionException
     */
    public function testShouldCheckIfCreateAdapterMethodIsProtected(): void
    {
        // When & Then
        $this->assertMethodIsProtected(RedisCacheTagPool::class, 'createAdapter');
    }

    /**
     * @return RedisCacheTagPool
     */
    private function createCachePool(): RedisCacheTagPool
    {
        return new RedisCacheTagPool($_ENV['REDIS_DSN']);
    }
}
