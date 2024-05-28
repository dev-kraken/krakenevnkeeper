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
        $this->expectExceptionMessageRegExp('.env file is empty');
        $env->load();
    }

    public function testNonReadableFile(): void
    {
        $path = __DIR__ . '/env/non-readable.env';
        touch($path);
        chmod($path, 0000); // Make the file unreadable

        $this->expectException(
            KrakenRuntimeException::class
        );
        $this->expectExceptionMessageRegExp('/.env file is not readable: ' . $path . '/');
        new KrakenEnvKeeper($path);
    }

    public function testNonExistentFile(): void
    {
        $this->expectException(
            KrakenRuntimeException::class
        ); 
        $this->expectExceptionMessageRegExp('/.env file not found at: ' . __DIR__ . '\/env\/non-existent.env/');
        new KrakenEnvKeeper(__DIR__ . '/env/non-existent.env');
    }

    public function testUpdateAfterChange(): void
    {
        $env = new KrakenEnvKeeper(__DIR__ . '/env/.env');
        $env->load();

        $originalValue = $env->get('APP_ENV');

        // Modify the .env file (simulate a change)
        file_put_contents(__DIR__ . '/env/.env', str_replace(
            'APP_ENV=testing',
            'APP_ENV=development',
            file_get_contents(__DIR__ . '/env/.env')
        ));

        // Load again, the cached value should be updated
        $env->load();
        $this->assertEquals('development', $env->get('APP_ENV'));

        // Restore the original content
        file_put_contents(__DIR__ . '/env/.env', str_replace(
            'APP_ENV=testing',
            'APP_ENV=' . $originalValue,
            file_get_contents(__DIR__ . '/env/.env')
        ));
    }

    private function expectExceptionMessageRegExp(string $string): void
    {
    }

}