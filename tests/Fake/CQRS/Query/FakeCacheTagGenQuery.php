<?php

declare(strict_types=1);

namespace PB\Component\Cache\Tests\Fake\CQRS\Query;

use PB\Component\Cache\CQRS\Query\CacheableQueryInterface;

/**
 * @author Paweł Brzeziński <pawel.brzezinski@smartint.pl>
 */
final class FakeCacheTagGenQuery extends FakeQuery implements CacheableQueryInterface
{
    /**
     * {@inheritDoc}
     */
    public function cacheKey(): string
    {
        return 'fake.cacheable_query.tag_gen.'.$this->id;
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
        return ['custom-tag-1'];
    }

    /**
     * {@inheritDoc}
     */
    public function cacheTagGeneratorId(): ?string
    {
        return 'fake.cacheable_query.tag_gen';
    }
}
