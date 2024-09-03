<?php

declare(strict_types=1);

namespace KrakenEnvKeeper\Tests;

use DevKraken\Exception\KrakenRuntimeException;
use DevKraken\KrakenEnvKeeper;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;

class KrakenEnvKeeperTest extends TestCase
{
    private const string ENV_DIR = __DIR__.'/env';
    private const string ENV_FILE = self::ENV_DIR.'/.env';

    #[Test]
    public function loadFromFile(): void
    {
        $env = new KrakenEnvKeeper(self::ENV_FILE);

        $this->assertEquals('development', $env->get('APP_ENV'));
        $this->assertEquals('localhost', $env->get('DB_HOST'));
    }

    #[Test]
    public function missingKeyShouldThrowException(): void
    {
        $env = new KrakenEnvKeeper(self::ENV_FILE);

        $this->expectException(KrakenRuntimeException::class);
        $this->expectExceptionMessage("Environment variable 'NON_EXISTENT_KEY' not found");
        $env->get('NON_EXISTENT_KEY');
    }

    #[Test]
    public function emptyFileShouldThrowException(): void
    {
        $emptyEnvFile = self::ENV_DIR.'/empty.env';
        $env = new KrakenEnvKeeper($emptyEnvFile);

        $this->expectException(KrakenRuntimeException::class);
        $this->expectExceptionMessage('.env file is empty');
        $env->load();
    }

    #[Test]
    public function nonReadableFileShouldThrowException(): void
    {
        $nonReadableFile = $this->createNonReadableFile();

        $this->expectException(KrakenRuntimeException::class);
        $this->expectExceptionMessage(".env file is not readable: $nonReadableFile");
        new KrakenEnvKeeper($nonReadableFile);
    }

    #[Test]
    public function nonExistentFileShouldThrowException(): void
    {
        $nonExistentFile = self::ENV_DIR.'/non-existent.env';

        $this->expectException(KrakenRuntimeException::class);
        $this->expectExceptionMessage(".env file not found at: $nonExistentFile");
        new KrakenEnvKeeper($nonExistentFile);
    }

    #[Test]
    public function updateAfterChange(): void
    {
        $env = new KrakenEnvKeeper(self::ENV_FILE);
        $originalValue = $env->get('APP_ENV');

        $this->modifyEnvFile('APP_ENV=development');
        $env->load();
        $this->assertEquals('development', $env->get('APP_ENV'));

        $this->modifyEnvFile("APP_ENV=$originalValue");
    }

    private function createNonReadableFile(): string
    {
        $path = self::ENV_DIR.'/non-readable.env';
        touch($path);
        chmod($path, 0000);
        return $path;
    }

    private function modifyEnvFile(string $newContent): void
    {
        $content = file_get_contents(self::ENV_FILE);
        $modifiedContent = preg_replace('/APP_ENV=.*/', $newContent, $content);
        file_put_contents(self::ENV_FILE, $modifiedContent);
    }
}