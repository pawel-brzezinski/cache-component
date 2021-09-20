<?php

declare(strict_types=1);

namespace PB\Component\Cache\Tests\Fake\TagGenerator;

use PB\Component\Cache\TagGenerator\TagGeneratorInterface;

/**
 * @author Paweł Brzeziński <pawel.brzezinski@smartint.pl>
 */
final class FakeFooTagGenerator implements TagGeneratorInterface
{
    /**
     * {@inheritDoc}
     */
    public function generate(object $object): array
    {
        return ['fake-foo-1', 'fake-foo-2'];
    }
}
