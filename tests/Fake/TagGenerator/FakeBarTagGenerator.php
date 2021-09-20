<?php

declare(strict_types=1);

namespace PB\Component\Cache\Tests\Fake\TagGenerator;

use PB\Component\Cache\TagGenerator\TagGeneratorInterface;

/**
 * @author Paweł Brzeziński <pawel.brzezinski@smartint.pl>
 */
final class FakeBarTagGenerator implements TagGeneratorInterface
{
    /**
     * {@inheritDoc}
     */
    public function generate(object $object): array
    {
        return ['fake-bar-1', 'fake-bar-2'];
    }
}
