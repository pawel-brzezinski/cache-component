<?php

declare(strict_types=1);

namespace PB\Component\Cache\Tests\Symfony\Messenger\Bus\Middleware;

use PB\Component\Cache\CQRS\Command\CacheKeyCommandInterface;
use PB\Component\Cache\Pool\CachePoolInterface;
use PB\Component\Cache\Symfony\Messenger\Bus\Middleware\InvalidateCacheKeyMiddleware;
use PB\Component\Cache\Tests\Fake\CQRS\Command\{FakeCacheKeyCommand, FakeCommand};
use PB\Component\Cache\Tests\TestCase\SymfonyMessengerMiddlewareTestCase;
use PB\Component\CQRS\Tests\TestComponent\Messenger\EnvelopeTestTrait;
use Prophecy\Argument;
use Prophecy\Prophecy\MethodProphecy;
use Psr\Cache\InvalidArgumentException;
use Symfony\Component\Messenger\Envelope;

/**
 * @author Paweł Brzeziński <pawel.brzezinski@smartint.pl>
 */
final class InvalidateCacheKeyMiddlewareTest extends SymfonyMessengerMiddlewareTestCase
{
    use EnvelopeTestTrait;

    ##########################################
    # InvalidateCacheKeyMiddleware::handle() #
    ##########################################

    /**
     * @return array
     */
    public function handleDataProvider(): array
    {
        // Dataset 1
        $message1 = new FakeCommand(1);
        $envelope1 = $this->generateMessageHandlerEnvelopeWithNoStamps($message1);
        $cacheKey1 = null;

        // Dataset 2
        $message2 = new FakeCacheKeyCommand(2);
        $envelope2 = $this->generateMessageHandlerEnvelopeWithNoStamps($message2);
        $cacheKey2 = 'fake.cache_key.command.id.2';

        return [
            'envelope message has no cache key interface' => [
                $message1, $envelope1, $cacheKey1,
            ],
            'envelope message has cache key interface' => [
                $message2, $envelope2, $cacheKey2,
            ],
        ];
    }

    /**
     * @dataProvider handleDataProvider
     *
     * @param object $message
     * @param Envelope $envelope
     * @param string|null $cacheKey
     *
     * @throws InvalidArgumentException
     *
     * @phpstan-ignore-next-line
     */
    public function testShouldCallHandleMethodAndCheckIfMessageCacheTagsHasBeenInvalidated(
        object $message,
        Envelope $envelope,
        ?string $cacheKey
    ): void {
        // Given

        // Mock CachePoolInterface::delete()
        if (true === $message instanceof CacheKeyCommandInterface) {
            /** @var MethodProphecy $methodProp */
            $methodProp = $this->cachePoolMock->delete($cacheKey);
            $methodProp->shouldBeCalledOnce()->willReturn(true);
        } else {
            /**
             * @var MethodProphecy $methodProp
             *
             * @noinspection PhpStrictTypeCheckingInspection
             * @phpstan-ignore-next-line
             */
            $methodProp = $this->cachePoolMock->delete(Argument::any());
            $methodProp->shouldNotBeCalled();
        }
        // End

        // Mock StackInterface::next()
        /** @var MethodProphecy $methodProp */
        $methodProp = $this->stackMock->next();
        $methodProp->shouldBeCalledOnce()->willReturn($this->nextMiddlewareMock->reveal());
        // End

        // Mock MiddlewareInterface::handle()
        $resultEnvelope = $this->generateMessageHandlerEnvelopeWithHandledStamp('result-handler', $message, null);

        /**
         * @var MethodProphecy $methodProp
         *
         * @noinspection PhpParamsInspection
         */
        $methodProp = $this->nextMiddlewareMock->handle($envelope, $this->stackMock->reveal());
        $methodProp->shouldBeCalledOnce()->willReturn($resultEnvelope);
        // End

        // When
        /** @noinspection PhpParamsInspection */
        $actual = $this->createMiddleware()->handle($envelope, $this->stackMock->reveal());

        // Then
        $this->assertSame($resultEnvelope, $actual);
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
     * @return InvalidateCacheKeyMiddleware
     */
    private function createMiddleware(): InvalidateCacheKeyMiddleware
    {
        /** @noinspection PhpParamsInspection */
        return new InvalidateCacheKeyMiddleware($this->cachePoolMock->reveal());
    }
}
