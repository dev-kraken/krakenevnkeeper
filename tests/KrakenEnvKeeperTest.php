<?php

namespace KrakenEnvKeeper\Tests;

use DevKraken\Exception\KrakenRuntimeException;
use DevKraken\KrakenEnvKeeper;
use PHPUnit\Framework\TestCase;

class KrakenEnvKeeperTest extends TestCase
{
    public function testLoadFromFile(): void
    {
        $env = new KrakenEnvKeeper(
            __DIR__ . '/env/.env'
        );  // Point to your example .env file
        $env->load();

        $this->assertEquals('development', $env->get('APP_ENV'));
        $this->assertEquals('localhost', $env->get('DB_HOST'));
        // Add more assertions for other expected environment variables
    }

    public function testMissingKey(): void
    {
        $env = new KrakenEnvKeeper(__DIR__ . '/env/.env');
        $env->load();

        $this->expectException(
            KrakenRuntimeException::class
        );  // Replace with your actual exception class
        $this->expectExceptionMessage(
            'Environment variable \'NON_EXISTENT_KEY\' not found'
        );
        $env->get('NON_EXISTENT_KEY');
    }

    public function testEmptyFile(): void
    {
        $env = new KrakenEnvKeeper(
            __DIR__ . '/env/empty.env'
        );  // Point to an empty .env file

        $this->expectException(
            KrakenRuntimeException::class
        );  // Replace with your actual exception class
        $this->expectExceptionMessage('.env file is empty');
        $env->load();
    }

}