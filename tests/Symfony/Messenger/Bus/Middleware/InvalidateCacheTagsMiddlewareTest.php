<?php

declare(strict_types=1);

namespace PB\Component\Cache\Tests\Symfony\Messenger\Bus\Middleware;

use PB\Component\Cache\CQRS\Command\CacheTagCommandInterface;
use PB\Component\Cache\Pool\CacheTagPoolInterface;
use PB\Component\Cache\Symfony\Messenger\Bus\Middleware\InvalidateCacheTagsMiddleware;
use PB\Component\Cache\Tests\Fake\CQRS\Command\{FakeCacheTagCommand, FakeCommand};
use PB\Component\Cache\Tests\TestCase\SymfonyMessengerMiddlewareTestCase;
use PB\Component\CQRS\Tests\TestComponent\Messenger\EnvelopeTestTrait;
use Prophecy\Argument;
use Prophecy\Prophecy\MethodProphecy;
use Symfony\Component\Messenger\Envelope;

/**
 * @author Paweł Brzeziński <pawel.brzezinski@smartint.pl>
 */
final class InvalidateCacheTagsMiddlewareTest extends SymfonyMessengerMiddlewareTestCase
{
    use EnvelopeTestTrait;

    ###########################################
    # InvalidateCacheTagsMiddleware::handle() #
    ###########################################

    /**
     * @return array
     */
    public function handleDataProvider(): array
    {
        // Dataset 1
        $message1 = new FakeCommand(1);
        $envelope1 = $this->generateMessageHandlerEnvelopeWithNoStamps($message1);
        $cacheTags1 = [];

        // Dataset 2
        $message2 = new FakeCacheTagCommand(2);
        $envelope2 = $this->generateMessageHandlerEnvelopeWithNoStamps($message2);
        $cacheTags2 = ['custom-tag-1', 'custom-tag-2'];

        return [
            'envelope message has no cache tag interface' => [
                $message1, $envelope1, $cacheTags1,
            ],
            'envelope message has cache tag interface' => [
                $message2, $envelope2, $cacheTags2,
            ],
        ];
    }

    /**
     * @dataProvider handleDataProvider
     *
     * @param object $message
     * @param Envelope $envelope
     * @param array $cacheTags
     */
    public function testShouldCallHandleMethodAndCheckIfMessageCacheTagsHasBeenInvalidated(
        object $message,
        Envelope $envelope,
        array $cacheTags
    ): void {
        // Given

        // Mock CachePoolInterface::invalidateTags()
        if (true === $message instanceof CacheTagCommandInterface) {
            /**
             * @var MethodProphecy $methodProp
             *
             * @phpstan-ignore-next-line
             */
            $methodProp = $this->cachePoolMock->invalidateTags($cacheTags);
            $methodProp->shouldBeCalledOnce()->willReturn(true);
        } else {
            /**
             * @var MethodProphecy $methodProp
             *
             * @noinspection PhpParamsInspection
             * @phpstan-ignore-next-line
             */
            $methodProp = $this->cachePoolMock->invalidateTags(Argument::any());
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
        return CacheTagPoolInterface::class;
    }

    /**
     * @return InvalidateCacheTagsMiddleware
     */
    private function createMiddleware(): InvalidateCacheTagsMiddleware
    {
        /** @noinspection PhpParamsInspection */
        return new InvalidateCacheTagsMiddleware($this->cachePoolMock->reveal());
    }
}
