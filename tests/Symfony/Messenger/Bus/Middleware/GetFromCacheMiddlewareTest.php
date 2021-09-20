<?php

declare(strict_types=1);

namespace PB\Component\Cache\Tests\Symfony\Messenger\Bus\Middleware;

use Closure;
use Exception;
use PB\Component\Cache\Exception\NotFoundInCacheException;
use PB\Component\Cache\Pool\CachePoolInterface;
use PB\Component\Cache\Symfony\Messenger\Bus\Middleware\GetFromCacheMiddleware;
use PB\Component\Cache\Tests\Fake\CQRS\Query\{FakeCacheNoTagGenQuery, FakeCacheTagGenQuery, FakeQuery};
use PB\Component\Cache\Tests\TestCase\SymfonyMessengerMiddlewareTestCase;
use PB\Component\CQRS\Tests\TestComponent\Messenger\EnvelopeTestTrait;
use PB\Component\FirstAid\Reflection\ReflectionHelper;
use Prophecy\Argument;
use Prophecy\Prophecy\MethodProphecy;
use Psr\Cache\InvalidArgumentException;
use Symfony\Component\Cache\CacheItem;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Stamp\HandledStamp;

/**
 * @author Paweł Brzeziński <pawel.brzezinski@smartint.pl>
 */
final class GetFromCacheMiddlewareTest extends SymfonyMessengerMiddlewareTestCase
{
    use EnvelopeTestTrait;

    ####################################
    # GetFromCacheMiddleware::handle() #
    ####################################

    /**
     * @return array
     */
    public function handleDataProvider(): array
    {
        // Dataset 1
        $message1 = new FakeQuery(1, 'Fake 1');
        $envelope1 = $this->generateMessageHandlerEnvelopeWithNoStamps($message1);
        $cacheKey1 = null;
        $cacheResult1 = null;

        // Dataset 2
        $message2 = new FakeCacheNoTagGenQuery(2, 'Fake 2');
        $envelope2 = $this->generateMessageHandlerEnvelopeWithNoStamps($message2);
        $cacheKey2 = 'fake.cacheable_query.no_tag_gen.2';
        $cacheResult2 = 'from-cache';

        // Dataset 3
        $message3 = new FakeCacheTagGenQuery(3, 'Fake 3');
        $envelope3 = $this->generateMessageHandlerEnvelopeWithNoStamps($message3);
        $cacheKey3 = 'fake.cacheable_query.tag_gen.3';
        $cacheResult3 = new NotFoundInCacheException($cacheKey3);

        return [
            'message is not cacheable' => [
                $message1, $envelope1, $cacheKey1, $cacheResult1,
            ],
            'message is cacheable and cache exist' => [
                $message2, $envelope2, $cacheKey2, $cacheResult2,
            ],
            'message is cacheable and cache not exist' => [
                $message3, $envelope3, $cacheKey3, $cacheResult3,
            ],
        ];
    }

    /**
     * @dataProvider handleDataProvider
     *
     * @param object $message
     * @param Envelope $envelope
     * @param string|null $cacheKey
     * @param mixed $cacheResult
     *
     * @throws InvalidArgumentException
     *
     * @phpstan-ignore-next-line
     */
    public function testShouldCallHandleMethodAndCheckIfCacheValueHasBeenSentIfExistInCache(
        object $message,
        Envelope $envelope,
        ?string $cacheKey,
        $cacheResult
    ): void
    {
        // Given
        $shouldBeFromHandler = null === $cacheKey || true === $cacheResult instanceof Exception;

        if (true === $shouldBeFromHandler) {
            // Mock CachePoolInterface::get()
            /**
             * @noinspection PhpParamsInspection
             * @noinspection PhpStrictTypeCheckingInspection
             * @phpstan-ignore-next-line
             */
            $this->cachePoolMock->get(Argument::any(), Argument::any())->shouldNotBeCalled();
            // End

            // Mock StackInterface::next()
            /** @var MethodProphecy $methodProp */
            $methodProp = $this->stackMock->next();
            $methodProp->shouldBeCalledOnce()->willReturn($this->nextMiddlewareMock->reveal());
            // End

            // Mock MiddlewareInterface::handle()
            $noCacheEnvelope = $this->generateMessageHandlerEnvelopeWithHandledStamp('result-handler', $message, 'not-from-cache');

            /**
             * @var MethodProphecy $methodProp
             *
             * @noinspection PhpParamsInspection
             */
            $methodProp =  $this->nextMiddlewareMock->handle($envelope, $this->stackMock->reveal());
            $methodProp->shouldBeCalledOnce()->willReturn($noCacheEnvelope);
            // End
        }

        if (null !== $cacheKey) {
            $assertCacheGetCallable = function (Closure $callable) use ($cacheKey) {
                $cacheItem = new CacheItem();
                ReflectionHelper::setPropertyValue($cacheItem, 'key', $cacheKey);

                try {
                    $callable($cacheItem);
                } catch (Exception $exception) {
                    return true;
                }

                return false;
            };

            /** @phpstan-ignore-next-line */
            if (false === $cacheResult instanceof Exception) {
                /** @noinspection PhpParamsInspection */
                $this->cachePoolMock->get($cacheKey, Argument::that($assertCacheGetCallable))->shouldBeCalledTimes(1)->willReturn($cacheResult);
            } else { /** @phpstan-ignore-line */
                /**
                 * @var MethodProphecy $methodProp
                 *
                 * @noinspection PhpParamsInspection
                 * @noinspection PhpStrictTypeCheckingInspection
                 * @phpstan-ignore-next-line
                 */
                $methodProp = $this->cachePoolMock->get(Argument::any(), Argument::any());
                $methodProp->shouldBeCalledTimes(1)->willThrow($cacheResult);
            }
        }

        // When & Then
        /** @noinspection PhpParamsInspection */
        $actual = $this->createMiddleware()->handle($envelope, $this->stackMock->reveal());
        /** @var HandledStamp $actualStamp */
        $actualStamp = $actual->last(HandledStamp::class);

        if (true === $shouldBeFromHandler) {
            $this->assertSame('result-handler::__invoke', $actualStamp->getHandlerName());
            $this->assertSame('not-from-cache', $actualStamp->getResult());
        } else {
            $this->assertSame(GetFromCacheMiddleware::class.'::handle', $actualStamp->getHandlerName());
            $this->assertSame($cacheResult, $actualStamp->getResult());
        }
    }

    #######
    # End #
    #######

    /**
     * {@inheritDoc}
     */
    protected function getCachePoolClass(): string
    {
        return CachePoolInterface::class;
    }

    /**
     * @return GetFromCacheMiddleware
     */
    private function createMiddleware(): GetFromCacheMiddleware
    {
        /** @noinspection PhpParamsInspection */
        return new GetFromCacheMiddleware($this->cachePoolMock->reveal());
    }
}
