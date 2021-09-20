<?php

declare(strict_types=1);

namespace PB\Component\Cache\Tests\Exception;

use PB\Component\Cache\Exception\NotFoundInCacheException;
use PHPUnit\Framework\TestCase;

/**
 * @author Paweł Brzeziński <pawel.brzezinski@smartint.pl>
 */
final class NotFoundInCacheExceptionTest extends TestCase
{
    ###########################################
    # NotFoundInCacheException::__construct() #
    ###########################################

    /**
     * @throws NotFoundInCacheException
     */
    public function testShouldThrowExceptionAndCheckIfMessageIsCorrect(): void
    {
        // Expect
        $this->expectException(NotFoundInCacheException::class);
        $this->expectExceptionMessage('Key "foobar" not found in cache.');

        // When
        throw new NotFoundInCacheException('foobar');
    }

    #######
    # End #
    #######
}
