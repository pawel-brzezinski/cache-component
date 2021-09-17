<?php

declare(strict_types=1);

namespace PB\Component\Cache\Tests\TestCase;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Dotenv\Dotenv;

/**
 * @author Paweł Brzeziński <pawel.brzezinski@smartint.pl>
 */
abstract class ComponentTestCase extends TestCase
{
    /**
     * @param string|null $name
     * @param array $data
     * @param string $dataName
     */
    public function __construct(?string $name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);

        $dotenv = new Dotenv();
        $dotenv->loadEnv(__DIR__.'/../.env');
    }
}
