<?php

declare(strict_types=1);

namespace PB\Component\Cache\Tests\Fake\Value;

use PB\Component\Cache\Value\CacheableValueInterface;

/**
 * @author Paweł Brzeziński <pawel.brzezinski@smartint.pl>
 */
final class FakeCacheableNoTagGenValue extends FakeValue implements CacheableValueInterface
{
    /**
     * {@inheritDoc}
     */
    public function cacheKey(): string
    {
        return 'fake.cacheable.no_tag_gen.'.$this->id;
    }

    /**
     * {@inheritDoc}
     */
    public function cacheLifetime(): int
    {
        return 60;
    }

    /**
     * {@inheritDoc}
     */
    public function cacheTags(): array
    {
        return ['tag-1', 'tag-2'];
    }

    /**
     * {@inheritDoc}
     */
    public function cacheTagGeneratorId(): ?string
    {
        return null;
    }
}
