<?php

declare(strict_types=1);

namespace PB\Component\Cache\Tests\Symfony\Messenger\Bus\Middleware;

use Assert\AssertionFailedException;
use Closure;
use PB\Component\Cache\Pool\{CachePoolInterface, CacheTagPoolInterface};
use PB\Component\Cache\Symfony\Messenger\Bus\Middleware\StoreInCacheMiddleware;
use PB\Component\Cache\Tests\Fake\CQRS\Query\{FakeCacheNoTagGenQuery, FakeCacheTagGenQuery, FakeQuery};
use PB\Component\CQRS\Tests\TestComponent\Messenger\EnvelopeTestTrait;
use PB\Component\FirstAid\Reflection\ReflectionHelper;
use Prophecy\Argument;
use Psr\Cache\InvalidArgumentException;
use stdClass;
use Symfony\Component\Cache\CacheItem;
use Symfony\Component\Messenger\Envelope;
use PB\Component\Cache\TagGenerator\{TagGeneratorInterface, TagGeneratorRegistryInterface};
use PB\Component\Cache\Tests\TestCase\SymfonyMessengerMiddlewareTestCase;
use Prophecy\Prophecy\{MethodProphecy, ObjectProphecy};

/**
 * @author Paweł Brzeziński <pawel.brzezinski@smartint.pl>
 */
final class StoreInCacheMiddlewareTest extends SymfonyMessengerMiddlewareTestCase
{
    use EnvelopeTestTrait;

    /** @var ObjectProphecy|TagGeneratorRegistryInterface|null  */
    private $tagGenRegMock;

    /** @var ObjectProphecy|TagGeneratorInterface|null */
    private $tagGenMock;

    /**
     * {@inheritDoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->tagGenRegMock = $this->prophesize(TagGeneratorRegistryInterface::class);
        $this->tagGenMock = $this->prophesize(TagGeneratorInterface::class);
    }

    /**
     * {@inheritDoc}
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        $this->tagGenRegMock = null;
        $this->tagGenMock = null;
    }

    ####################################
    # StoreInCacheMiddleware::handle() #
    ####################################

    /**
     * @return array
     */
    public function handleDataProvider(): array
    {
        // Dataset 1
        $cachePoolClass1 = CachePoolInterface::class;
        $message1 = new FakeQuery(1, 'Fake 1');
        $handlerResult1 = 'some result 1';
        $envelope1 = $this->generateMessageHandlerEnvelopeWithHandledStamp('fake-handler-1', $message1, $handlerResult1);
        $cacheKey1 = null;
        $tagGenId1 = null;
        $tagGenTags1 = [];
        $cacheTags1 = [];

        // Dataset 2
        $cachePoolClass2 = CachePoolInterface::class;
        $message2 = new FakeCacheNoTagGenQuery(2, 'Fake 2');
        $handlerResult2 = 'some result 2';
        $envelope2 = $this->generateMessageHandlerEnvelopeWithDelayStamp($message2, 200);
        $cacheKey2 = null;
        $tagGenId2 = null;
        $tagGenTags2 = [];
        $cacheTags2 = [];

        // Dataset 3
        $cachePoolClass3 = CacheTagPoolInterface::class;
        $message3 = new FakeCacheNoTagGenQuery(3, 'Fake 3');
        $handlerResult3 = 'some result 3';
        $envelope3 = $this->generateMessageHandlerEnvelopeWithHandledStamp('fake-handler-3', $message3, $handlerResult3);
        $cacheKey3 = 'fake.cacheable_query.no_tag_gen.3';
        $tagGenId3 = null;
        $tagGenTags3 = [];
        $cacheTags3 = ['tag-1' => 'tag-1', 'tag-2' => 'tag-2'];

        // Dataset 4
        $cachePoolClass4 = CachePoolInterface::class;
        $message4 = new FakeCacheNoTagGenQuery(4, 'Fake 4');
        $handlerResult4 = 'some result 3';
        $envelope4 = $this->generateMessageHandlerEnvelopeWithHandledStamp('fake-handler-4', $message4, $handlerResult4);
        $cacheKey4 = 'fake.cacheable_query.no_tag_gen.4';
        $tagGenId4 = null;
        $tagGenTags4 = [];
        $cacheTags4 = [];

        // Dataset 5
        $cachePoolClass5 = CacheTagPoolInterface::class;
        $message5 = new FakeCacheTagGenQuery(5, 'Fake 5');
        $handlerResult5 = new stdClass();
        $handlerResult5->name = 'Fake 5';
        $envelope5 = $this->generateMessageHandlerEnvelopeWithHandledStamp('fake-handler-5', $message5, $handlerResult5);
        $cacheKey5 = 'fake.cacheable_query.tag_gen.5';
        $tagGenId5 = 'fake.cacheable_query.tag_gen';
        $tagGenTags5 = ['tag-gen-1', 'tag-gen-2'];
        $cacheTags5 = ['custom-tag-1' => 'custom-tag-1', 'tag-gen-1' => 'tag-gen-1', 'tag-gen-2' => 'tag-gen-2'];

        // Dataset 6
        $cachePoolClass6 = CachePoolInterface::class;
        $message6 = new FakeCacheTagGenQuery(6, 'Fake 6');
        $handlerResult6 = new stdClass();
        $handlerResult6->name = 'Fake 6';
        $envelope6 = $this->generateMessageHandlerEnvelopeWithHandledStamp('fake-handler-6', $message6, $handlerResult6);
        $cacheKey6 = 'fake.cacheable_query.tag_gen.6';
        $tagGenId6 = 'fake.cacheable_query.tag_gen';
        $tagGenTags6 = [];
        $cacheTags6 = [];

        return [
            'envelope has handled stamp and message is not cacheable' => [
                $cachePoolClass1, $message1, $handlerResult1, $envelope1, $cacheKey1, $tagGenId1, $tagGenTags1, $cacheTags1,
            ],
            'envelope has not handled stamp and message is cacheable and message has not tag generator' => [
                $cachePoolClass2, $message2, $handlerResult2, $envelope2, $cacheKey2, $tagGenId2, $tagGenTags2, $cacheTags2,
            ],
            'envelope has handled stamp and message is cacheable and message has not tag generator and cache item is taggable' => [
                $cachePoolClass3, $message3, $handlerResult3, $envelope3, $cacheKey3, $tagGenId3, $tagGenTags3, $cacheTags3,
            ],
            'envelope has handled stamp and message is cacheable and message has not tag generator and cache item is not taggable' => [
                $cachePoolClass4, $message4, $handlerResult4, $envelope4, $cacheKey4, $tagGenId4, $tagGenTags4, $cacheTags4,
            ],
            'envelope has handled stamp and message is cacheable and message has tag generator and cache item is taggable' => [
                $cachePoolClass5, $message5, $handlerResult5, $envelope5, $cacheKey5, $tagGenId5, $tagGenTags5, $cacheTags5,
            ],
            'envelope has handled stamp and message is cacheable and message has tag generator and cache item is not taggable' => [
                $cachePoolClass6, $message6, $handlerResult6, $envelope6, $cacheKey6, $tagGenId6, $tagGenTags6, $cacheTags6,
            ],
        ];
    }

    /**
     * @dataProvider handleDataProvider
     *
     * @param string $cachePoolClass
     * @param object $message
     * @param mixed $handlerResult
     * @param Envelope $envelope
     * @param string|null $cacheKey
     * @param string|null $tagGenId
     * @param array $tagGenTags
     * @param array $cacheTags
     *
     * @throws AssertionFailedException
     * @throws InvalidArgumentException
     *
     * @phpstan-ignore-next-line
     */
    public function testShouldCallHandleMethodAndCheckIfHandledMessageHasBeenStoredInCacheIfIsCacheable(
        string $cachePoolClass,
        object $message,
        $handlerResult,
        Envelope $envelope,
        ?string $cacheKey,
        ?string $tagGenId,
        array $tagGenTags,
        array $cacheTags
    ): void {
        // Given
        $isTaggable = CacheTagPoolInterface::class === $cachePoolClass;
        $middlewareUnderTest = $this->createMiddleware($cachePoolClass);

        // Mock TagGeneratorInterface::generate()
        if (true === $isTaggable && null !== $tagGenId) {
            /** @var MethodProphecy $methodProp */
            $methodProp = $this->tagGenMock->generate($handlerResult);
            $methodProp->shouldBeCalledOnce()->willReturn($tagGenTags);
        } else {
            /** @var MethodProphecy $methodProp */
            $methodProp = $this->tagGenMock->generate(Argument::any());
            $methodProp->shouldNotBeCalled();
        }
        // End

        // Mock TagGeneratorRegistryInterface::get()
        if (true === $isTaggable && null !== $tagGenId) {
            /** @var MethodProphecy $methodProp */
            $methodProp = $this->tagGenRegMock->get($tagGenId);
            $methodProp->shouldBeCalledOnce()->willReturn($this->tagGenMock->reveal());
        } else {
            /**
             * @var MethodProphecy $methodProp
             *
             * @noinspection PhpStrictTypeCheckingInspection
             * @phpstan-ignore-next-line
             */
            $methodProp = $this->tagGenRegMock->get(Argument::any());
            $methodProp->shouldNotBeCalled();
        }
        // End

        // Mock CachePoolInterface::get()
        if (null !== $cacheKey) {
            $assertCacheGetCallable = function (Closure $callable) use ($handlerResult, $isTaggable, $cacheTags) {
                $cacheItem = new CacheItem();
                ReflectionHelper::setPropertyValue($cacheItem, 'isTaggable', $isTaggable);

                $result = $callable($cacheItem);
                $itemCacheExpiry = ReflectionHelper::getPropertyValue($cacheItem, 'expiry');
                $itemCacheTags = ReflectionHelper::getPropertyValue($cacheItem, 'newMetadata');

                $expectedCacheTags = true === $isTaggable ? ['tags' => $cacheTags] : [];

                return $handlerResult === $result
                    && null !== $itemCacheExpiry
                    && $expectedCacheTags === $itemCacheTags
                ;
            };

            /**
             * @var MethodProphecy $methodProp
             *
             * @noinspection PhpParamsInspection
             */
            $methodProp = $this->cachePoolMock->get($cacheKey, Argument::that($assertCacheGetCallable));
            $methodProp->shouldBeCalledOnce()->willReturn($handlerResult);
        } else {
            /**
             * @var MethodProphecy $methodProp
             *
             * @noinspection PhpParamsInspection
             * @noinspection PhpStrictTypeCheckingInspection
             * @phpstan-ignore-next-line
             */
            $methodProp = $this->cachePoolMock->get(Argument::any(), Argument::any());
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
        $actual = $middlewareUnderTest->handle($envelope, $this->stackMock->reveal());

        // Then
        $this->assertSame($resultEnvelope, $actual);
    }

    #######
    # End #
    #######

    /**
     * @return string
     */
    protected function getCachePoolClass(): string
    {
        return CacheTagPoolInterface::class;
    }

    /**
     * @param string|null $cachePoolClass
     *
     * @return StoreInCacheMiddleware
     */
    private function createMiddleware(string $cachePoolClass = null): StoreInCacheMiddleware
    {
        if (null !== $cachePoolClass) {
            $this->cachePoolMock = $this->prophesize($cachePoolClass);
        }

        /** @noinspection PhpParamsInspection */
        return new StoreInCacheMiddleware($this->cachePoolMock->reveal(), $this->tagGenRegMock->reveal());
    }
}
