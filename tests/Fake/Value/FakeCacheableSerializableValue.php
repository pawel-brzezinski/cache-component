<?php

declare(strict_types=1);

namespace PB\Component\Cache\Tests\Fake\Value;

use PB\Component\Cache\Value\CacheableValueInterface;
use PB\Component\Cache\Value\SerializableCacheValueInterface;
use PB\Component\FirstAid\Accessor\ValueAccessorTrait;

/**
 * @author Paweł Brzeziński <pawel.brzezinski@smartint.pl>
 */
final class FakeCacheableSerializableValue extends FakeValue implements CacheableValueInterface, SerializableCacheValueInterface
{
    use ValueAccessorTrait;

    /**
     * @return FakeCacheableSerializableValue
     */
     public static function deserializeFromCacheValue(array $data): object
     {
         return new self($data['id'], $data['name']);
     }

    /**
     * {@inheritDoc}
     */
    public function cacheKey(): string
    {
        return 'fake.cacheable_serializable.tag_gen.id.'.$this->id;
    }

    /**
     * {@inheritDoc}
     */
    public function cacheLifetime(): int
    {
        return 180;
    }

    /**
     * {@inheritDoc}
     */
    public function cacheTags(): array
    {
        return [];
    }

    /**
     * {@inheritDoc}
     */
    public function cacheTagGeneratorId(): ?string
    {
        return 'fake_cacheable_serializable_tag_gen';
    }

    /**
     * {@inheritDoc}
     */
    public function serializeToCacheValue(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
        ];
    }
}
