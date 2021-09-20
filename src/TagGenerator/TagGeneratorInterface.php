<?php

declare(strict_types=1);

namespace PB\Component\Cache\TagGenerator;

/**
 * @author Paweł Brzeziński <pawel.brzezinski@smartint.pl>
 */
interface TagGeneratorInterface
{
    /**
     * @param object $object
     *
     * @return string[]
     */
    public function generate(object $object): array;
}
