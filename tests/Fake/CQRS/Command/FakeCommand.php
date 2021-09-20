<?php

declare(strict_types=1);

namespace PB\Component\Cache\Tests\Fake\CQRS\Command;

use PB\Component\CQRS\Command\CommandInterface;
use PB\Component\FirstAid\Accessor\ValueAccessorTrait;

/**
 * @author Paweł Brzeziński <pawel.brzezinski@smartint.pl>
 *
 * @method int id()
 */
class FakeCommand implements CommandInterface
{
    use ValueAccessorTrait;

    protected int $id;

    /**
     * @param int $id
     */
    public function __construct(int $id)
    {
        $this->id = $id;
    }
}
