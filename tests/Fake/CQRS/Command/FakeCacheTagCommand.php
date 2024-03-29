<?php

declare(strict_types=1);

namespace PB\Component\Cache\Tests\Fake\CQRS\Command;

use PB\Component\Cache\CQRS\Command\CacheTagCommandInterface;

/**
 * @author Paweł Brzeziński <pawel.brzezinski@smartint.pl>
 */
final class FakeCacheTagCommand extends FakeCommand implements CacheTagCommandInterface
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
}
