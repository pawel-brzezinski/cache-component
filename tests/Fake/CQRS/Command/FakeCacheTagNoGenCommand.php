<?php

declare(strict_types=1);

namespace PB\Component\Cache\Tests\Fake\CQRS\Command;

use PB\Component\Cache\CQRS\Command\CacheTagCommandInterface;

/**
 * @author Paweł Brzeziński <pawel.brzezinski@smartint.pl>
 */
final class FakeCacheTagNoGenCommand implements CacheTagCommandInterface
{
    /**
     * @return string[]
     */
    public function cacheTags(): array
    {
        return [
            'custom-tag-1',
            'custom-tag-2',
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function cacheTagGenerator(): ?string
    {
        return null;
    }
}
