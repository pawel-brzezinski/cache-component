<?php

declare(strict_types=1);

namespace PB\Component\Cache\Symfony\Messenger\Bus\Middleware;

use Exception;
use PB\Component\Cache\CQRS\Query\CacheableQueryInterface;
use PB\Component\Cache\Exception\NotFoundInCacheException;
use PB\Component\Cache\Pool\CachePoolInterface;
use Psr\Cache\InvalidArgumentException;
use Symfony\Component\Cache\CacheItem;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Middleware\{MiddlewareInterface, StackInterface};
use Symfony\Component\Messenger\Handler\HandlerDescriptor;
use Symfony\Component\Messenger\Stamp\HandledStamp;

/**
 * @author Paweł Brzeziński <pawel.brzezinski@smartint.pl>
 */
final class GetFromCacheMiddleware implements MiddlewareInterface
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
     * @return Envelope
     *
     * @throws InvalidArgumentException
     *
     * @phpstan-ignore-next-line
     */
    public function handle(Envelope $envelope, StackInterface $stack): Envelope
    {
        $message = $envelope->getMessage();

        if (false === $message instanceof CacheableQueryInterface) {
            return $stack->next()->handle($envelope, $stack);
        }

        try {
            $cacheResult = $this->cachePool->get($message->cacheKey(), function (CacheItem $cacheItem) {
                throw new NotFoundInCacheException($cacheItem->getKey());
            });
        } catch (NotFoundInCacheException $exception) {
            return $stack->next()->handle($envelope, $stack);
        }

        $descriptor = new HandlerDescriptor([$this, 'handle']);

        return $envelope->with(HandledStamp::fromDescriptor($descriptor, $cacheResult));
    }
}
