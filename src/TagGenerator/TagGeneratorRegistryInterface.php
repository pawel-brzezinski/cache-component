<?php

declare(strict_types=1);

namespace PB\Component\Cache\TagGenerator;

use Assert\AssertionFailedException;

/**
 * @author Paweł Brzeziński <pawel.brzezinski@smartint.pl>
 */
interface TagGeneratorRegistryInterface
{
    /**
     * @param string $name
     *
     * @return TagGeneratorInterface
     *
     * @throws AssertionFailedException
     */
    public function get(string $name): TagGeneratorInterface;
}
