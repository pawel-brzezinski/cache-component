<?php

declare(strict_types=1);

namespace PB\Component\Cache\Tests\TestCase;

use PB\Component\Cache\Pool\{CachePoolInterface, CacheTagPoolInterface};
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use Symfony\Component\Messenger\Middleware\{MiddlewareInterface, StackInterface};

/**
 * @author Paweł Brzeziński <pawel.brzezinski@smartint.pl>
 */
abstract class SymfonyMessengerMiddlewareTestCase extends TestCase
{
    use ProphecyTrait;

    /** @var ObjectProphecy|CachePoolInterface|CacheTagPoolInterface|null  */
    protected $cachePoolMock;

    /** @var ObjectProphecy|StackInterface|null  */
    protected $stackMock;

    /** @var ObjectProphecy|MiddlewareInterface|null  */
    protected $nextMiddlewareMock;

    /**
     * {@inheritDoc}
     */
    protected function setUp(): void
    {
        $this->cachePoolMock = $this->prophesize($this->getCachePoolClass());
        $this->stackMock = $this->prophesize(StackInterface::class);
        $this->nextMiddlewareMock = $this->prophesize(MiddlewareInterface::class);
    }

    /**
     * {@inheritDoc}
     */
    protected function tearDown(): void
    {
        $this->cachePoolMock = null;
        $this->stackMock = null;
        $this->nextMiddlewareMock = null;
    }

    /**
     * @return string
     */
    abstract protected function getCachePoolClass(): string;
}
