<?php

declare(strict_types=1);

namespace PB\Component\Cache\Symfony\Messenger\Bus\Middleware;

use PB\Component\Cache\CQRS\Command\CacheKeyCommandInterface;
use PB\Component\Cache\Pool\CachePoolInterface;
use Psr\Cache\InvalidArgumentException;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Middleware\{MiddlewareInterface, StackInterface};

/**
 * @author Paweł Brzeziński <pawel.brzezinski@smartint.pl>
 */
final class InvalidateCacheKeyMiddleware implements MiddlewareInterface
{
    private CachePoolInterface $cachePool;

    /**
     * @param CachePoolInterface $cachePool
     */
    public function __construct(CachePoolInterface $cachePool)
    {
        $this->cachePool = $cachePool;
    }

    /**
     * @param Envelope $envelope
     * @param StackInterface $stack
     *
     * @return Envelope
     *
     * @throws InvalidArgumentException
     *
     * @phpstan-ignore-next-line
     */
    public function handle(Envelope $envelope, StackInterface $stack): Envelope
    {
        $message = $envelope->getMessage();

        if (true === $message instanceof CacheKeyCommandInterface) {
            $this->cachePool->delete($message->cacheKey());
        }

        return $stack->next()->handle($envelope, $stack);
    }
}
