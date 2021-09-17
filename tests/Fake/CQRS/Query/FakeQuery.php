<?php

declare(strict_types=1);

namespace PB\Component\Cache\Tests\Fake\CQRS\Query;

use PB\Component\FirstAid\Accessor\ValueAccessorTrait;

/**
 * @author Paweł Brzeziński <pawel.brzezinski@smartint.pl>
 *
 * @method int id()
 * @method string name()
 */
class FakeQuery
{
    use ValueAccessorTrait;

    protected int $id;

    private string $name;

    /**
     * @param int $id
     * @param string $name
     */
    public function __construct(int $id, string $name)
    {
        $this->id = $id;
        $this->name = $name;
    }
}
