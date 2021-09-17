<?php

declare(strict_types=1);

namespace PB\Component\Cache\CQRS\Query;

use PB\Component\Cache\Value\CacheableValueInterface;
use PB\Component\CQRS\Query\QueryInterface;

/**
 * Interface for cacheable query message implementation.
 *
 * @author Paweł Brzeziński <pawel.brzezinski@smartint.pl>
 */
interface CacheableQueryInterface extends CacheableValueInterface, QueryInterface
{

}
