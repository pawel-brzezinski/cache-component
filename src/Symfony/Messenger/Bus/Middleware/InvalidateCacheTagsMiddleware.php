<?php

declare(strict_types=1);

namespace PB\Component\Cache\Symfony\Messenger\Bus\Middleware;

use PB\Component\Cache\CQRS\Command\CacheTagCommandInterface;
use PB\Component\Cache\Pool\CacheTagPoolInterface;
use Symfony\Component\Messenger\Middleware\{MiddlewareInterface, StackInterface};
use Symfony\Component\Messenger\Envelope;

/**
 * @author Paweł Brzeziński <pawel.brzezinski@smartint.pl>
 */
final class InvalidateCacheTagsMiddleware implements MiddlewareInterface
{
    private CacheTagPoolInterface $cachePool;

    /**
     * @param CacheTagPoolInterface $cachePool
     */
    public function __construct(CacheTagPoolInterface $cachePool)
    {
        $this->cachePool = $cachePool;
    }

    /**
     * @param Envelope $envelope
     * @param StackInterface $stack
     *
     * @return Envelope
     */
    public function handle(Envelope $envelope, StackInterface $stack): Envelope
    {
        $message = $envelope->getMessage();

        if (true === $message instanceof CacheTagCommandInterface) {
            $this->cachePool->invalidateTags($message->cacheTags());
        }

        return $stack->next()->handle($envelope, $stack);
    }
}
