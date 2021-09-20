<?php

declare(strict_types=1);

namespace PB\Component\Cache\TagGenerator;

use Assert\Assertion;

/**
 * @author Paweł Brzeziński <pawel.brzezinski@smartint.pl>
 */
final class TagGeneratorRegistry implements TagGeneratorRegistryInterface
{
    private const NOT_FOUND_ERROR_MESSAGE = 'Tag generator "%s" does not exist in registry.';

    private iterable $tagGenerators;

    /**
     * TagGeneratorRegistry constructor.
     *
     * @param iterable $tagGenerators
     */
    public function __construct(iterable $tagGenerators)
    {
        /** @noinspection PhpParamsInspection */
        Assertion::allIsInstanceOf($tagGenerators, TagGeneratorInterface::class);

        $this->tagGenerators = $tagGenerators;
    }

    /**
     * {@inheritDoc}
     */
    public function get(string $name): TagGeneratorInterface
    {
        /** @var TagGeneratorInterface|null $generator */
        $generator = null;

        Assertion::satisfy($this->tagGenerators, function (iterable $tagGenerators) use ($name, &$generator) {
            foreach ($tagGenerators as $tagGenerator) {
                if ($name === get_class($tagGenerator)) {
                    $generator = $tagGenerator;
                    return true;
                }
            }

            return false;
        }, sprintf(self::NOT_FOUND_ERROR_MESSAGE, $name));

        return $generator;
    }
}
