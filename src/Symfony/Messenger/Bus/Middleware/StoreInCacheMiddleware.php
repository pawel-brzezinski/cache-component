<?php

declare(strict_types=1);

namespace PB\Component\Cache\Symfony\Messenger\Bus\Middleware;

use Assert\AssertionFailedException;
use PB\Component\Cache\CQRS\Query\CacheableQueryInterface;
use PB\Component\Cache\Pool\{CachePoolInterface, CacheTagPoolInterface};
use PB\Component\Cache\TagGenerator\TagGeneratorRegistryInterface;
use Psr\Cache\InvalidArgumentException;
use Symfony\Component\Cache\CacheItem;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Middleware\{MiddlewareInterface, StackInterface};
use Symfony\Component\Messenger\Stamp\HandledStamp;

/**
 * @author Paweł Brzeziński <pawel.brzezinski@smartint.pl>
 */
final class StoreInCacheMiddleware implements MiddlewareInterface
{
    private CachePoolInterface $cachePool;

    private TagGeneratorRegistryInterface $tagGeneratorRegistry;

    /**
     * @param CachePoolInterface $cachePool
     * @param TagGeneratorRegistryInterface $tagGeneratorRegistry
     */
    public function __construct(CachePoolInterface $cachePool, TagGeneratorRegistryInterface $tagGeneratorRegistry)
    {
        $this->cachePool = $cachePool;
        $this->tagGeneratorRegistry = $tagGeneratorRegistry;
    }

    /**
     * @param Envelope $envelope
     * @param StackInterface $stack
     *
     * @return Envelope
     *
     * @throws AssertionFailedException
     * @throws InvalidArgumentException
     *
     * @phpstan-ignore-next-line
     */
    public function handle(Envelope $envelope, StackInterface $stack): Envelope
    {
        $handled = $envelope->last(HandledStamp::class);
        $message = $envelope->getMessage();

        if (true === $message instanceof CacheableQueryInterface && true === $handled instanceof HandledStamp) {
            $handledResult = $handled->getResult();
            $cacheLifetime = $message->cacheLifetime();
            $cacheTags = $message->cacheTags();

            if (true === $this->cachePool instanceof CacheTagPoolInterface && null !== $tagGeneratorClassname = $message->cacheTagGeneratorId()) {
                $cacheTags = array_merge(
                    $cacheTags,
                    $this->tagGeneratorRegistry->get($tagGeneratorClassname)->generate($handledResult)
                );
            }

            $this->cachePool->get($message->cacheKey(), function (CacheItem $cacheItem) use ($handledResult, $cacheLifetime, $cacheTags) {
                $cacheItem->expiresAfter($cacheLifetime);

                if (true === $this->cachePool instanceof CacheTagPoolInterface) {
                    $cacheItem->tag($cacheTags);
                }

                return $handledResult;
            });
        }

        return $stack->next()->handle($envelope, $stack);
    }
}
