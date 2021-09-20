<?php

declare(strict_types=1);

namespace PB\Component\Cache\Tests\TagGenerator;

use ArrayIterator;
use Assert\AssertionFailedException;
use PB\Component\Cache\Tests\Fake\TagGenerator\{FakeBarTagGenerator, FakeFooTagGenerator};
use PB\Component\Cache\TagGenerator\{TagGeneratorInterface, TagGeneratorRegistry};
use PB\Component\FirstAid\Reflection\ReflectionHelper;
use PHPUnit\Framework\TestCase;
use ReflectionException;
use stdClass;

/**
 * @author Paweł Brzeziński <pawel.brzezinski@smartint.pl>
 */
final class TagGeneratorRegistryTest extends TestCase
{
    #######################################
    # TagGeneratorRegistry::__construct() #
    #######################################

    /**
     * @return array
     */
    public function constructDataProvider(): array
    {
        // Dataset 1
        $tagGenerators1 = new ArrayIterator([
            new FakeFooTagGenerator(),
            new FakeBarTagGenerator(),
        ]);
        $expected1 = $tagGenerators1;
        $expectError1 = false;

        // Dataset 2
        $tagGenerators2 = new ArrayIterator([
            new FakeFooTagGenerator(),
            new FakeBarTagGenerator(),
            new stdClass()
        ]);
        $expected2 = null;
        $expectError2 = true;

        return [
            'tag generators iterator contain only valid generators' => [$tagGenerators1, $expected1, $expectError1],
            'tag generators iterator contain at least one not valid generator' => [$tagGenerators2, $expected2, $expectError2],
        ];
    }

    /**
     * @dataProvider constructDataProvider
     *
     * @param iterable $tagGenerators
     * @param iterable|null $expected
     * @param bool|null $expectError
     *
     * @throws ReflectionException
     */
    public function testShouldCreateRegistryInstanceAndCheckIfTagGeneratorArrayHasBeenValidatedAndSetInProperty(
        iterable $tagGenerators,
        ?iterable $expected,
        ?bool $expectError
    ): void {
        // Expect
        if (true === $expectError) {
            $this->expectException(AssertionFailedException::class);
        }

        // When
        $actual = new TagGeneratorRegistry($tagGenerators);

        // Then
        if (false === $expectError) {
            $this->assertSame($expected, ReflectionHelper::getPropertyValue($actual, 'tagGenerators'));
        }
    }

    #######
    # End #
    #######

    ###############################
    # TagGeneratorRegistry::get() #
    ###############################

    /**
     * @return array
     */
    public function getDataProvider(): array
    {
        // Dataset 1
        $tagGenerators1 = new ArrayIterator([
            new FakeFooTagGenerator(),
            new FakeBarTagGenerator(),
        ]);
        $className1 = FakeBarTagGenerator::class;
        $expected1 = $tagGenerators1[1];
        $expectErrorMessage1 = null;

        // Dataset 2
        $tagGenerators2 = new ArrayIterator([
            new FakeBarTagGenerator(),
        ]);
        $className2 = FakeFooTagGenerator::class;
        $expected2 = null;
        $expectErrorMessage2 = 'Tag generator "'.$className2.'" does not exist in registry.';

        return [
            'given tag generator exist in registry' => [$tagGenerators1, $className1, $expected1, $expectErrorMessage1],
            'given tag generator not exist in registry' => [$tagGenerators2, $className2, $expected2, $expectErrorMessage2],
        ];
    }

    /**
     * @dataProvider getDataProvider
     *
     * @param iterable $tagGenerators
     * @param string $className
     * @param TagGeneratorInterface|null $expected
     * @param string|null $expectErrorMessage
     */
    public function testShouldCallGetMethodAndCheckIfCorrectTagGeneratorHasBeenReturnedIfExistOrExceptionHasBeenThrownWhenNotExist(
        iterable $tagGenerators,
        string $className,
        ?TagGeneratorInterface $expected,
        ?string $expectErrorMessage
    ): void {
        // Expect
        if (null !== $expectErrorMessage) {
            $this->expectException(AssertionFailedException::class);
            $this->expectExceptionMessage($expectErrorMessage);
        }

        // Given
        $registryUnderTest = new TagGeneratorRegistry($tagGenerators);

        // When
        $actual = $registryUnderTest->get($className);

        // Then
        if (null === $expectErrorMessage) {
            $this->assertSame($expected, $actual);
        }
    }

    #######
    # End #
    #######
}
