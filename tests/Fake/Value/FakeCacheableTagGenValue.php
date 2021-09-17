<?php

declare(strict_types=1);

namespace PB\Component\Cache\Tests\Fake\Value;

use PB\Component\Cache\Value\CacheableValueInterface;

/**
 * @author Paweł Brzeziński <pawel.brzezinski@smartint.pl>
 */
final class FakeCacheableTagGenValue extends FakeValue implements CacheableValueInterface
{
    /**
     * {@inheritDoc}
     */
    public function cacheKey(): string
    {
        return 'fake.cacheable.tag_gen.'.$this->id;
    }

    /**
     * {@inheritDoc}
     */
    public function cacheLifetime(): int
    {
        return 120;
    }

    /**
     * {@inheritDoc}
     */
    public function cacheTags(): array
    {
        return ['custom-tag-1'];
    }

    /**
     * {@inheritDoc}
     */
    public function cacheTagGeneratorId(): ?string
    {
        return 'fake.cacheable.tag_gen';
    }
}
