<?php

declare(strict_types=1);

namespace DevKraken;

use DevKraken\Exception\KrakenRuntimeException;
use KrakenInterface\EnvironmentInterface;

class KrakenEnvKeeper implements EnvironmentInterface
{
    private string $path;
    private array $cache = [];
    private int|false $lastModified;

    public function __construct(string $path)
    {
        $this->path = $path;
        $this->validateFile($path);
        $this->lastModified = filemtime($path);
    }

    private function validateFile(string $path): void
    {
        if (!file_exists($path)) {
            throw new KrakenRuntimeException(sprintf('.env file not found at: %s', $path));
        }

        if (!is_readable($path)) {
            throw new KrakenRuntimeException(sprintf('.env file is not readable: %s', $path));
        }
    }

    public function load(): void
    {
        if (!empty($this->cache) && !$this->needsUpdate()) {
            return;
        }
        $this->cache = $this->parseFile($this->path);
        $this->lastModified = filemtime($this->path);

        if (empty($this->cache)) {
            throw new KrakenRuntimeException('.env file is empty');
        }
    }

    private function needsUpdate(): bool
    {
        return !file_exists($this->path) || filemtime($this->path) !== $this->lastModified;
    }

    public function get(string $key, mixed $default = null): ?string
    {
        $this->load();
        if (!array_key_exists($key, $this->cache)) {
            if (is_null($default)) {
                throw new KrakenRuntimeException("Environment variable '$key' not found");
            }
            return $default;
        }
        return $this->cache[$key] ?? null;
    }

    public function has(string $key): bool
    {
        $this->load();
        return isset($this->cache[$key]);
    }

    private function parseFile(string $path): array
    {
        $lines = array_filter(
            file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES),
            static fn($line) => !str_starts_with(trim($line), '#')
        );

        $data = [];
        foreach ($lines as $line) {
            [$key, $value] = explode('=', $line, 2);
            $data[trim($key)] = trim($value);
        }
        return $data;
    }
}