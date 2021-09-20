<?php

declare(strict_types=1);

namespace PB\Component\Cache\Tests\Fake\CQRS\Command;

use PB\Component\Cache\CQRS\Command\CacheKeyCommandInterface;

/**
 * @author Paweł Brzeziński <pawel.brzezinski@smartint.pl>
 */
final class FakeCacheKeyCommand extends FakeCommand implements CacheKeyCommandInterface
{
    /**
     * {@inheritDoc}
     */
    public function cacheKey(): string
    {
        return 'fake.cache_key.command.id.'.$this->id;
    }
}
