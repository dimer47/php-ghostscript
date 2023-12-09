<?php

namespace Tests;

use Dotenv\Dotenv;
use PHPUnit\Framework\TestCase;

abstract class BaseTest extends TestCase
{
    /**
     * @return void
     */
    protected function setUp(): void
    {
        $dotenv = Dotenv::createImmutable(dirname(__DIR__));
        $dotenv->safeLoad();

        parent::setUp();
    }

    /**
     * @param string $key
     * @param string $default
     *
     * @return string|bool|int|float|null
     */
    protected function getEnv(string $key, string $default = '')
    {
        return $_ENV[$key] ?? $default;
    }
}
