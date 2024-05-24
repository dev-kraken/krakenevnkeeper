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
        $this->lastModified = filemtime($path); // Store last modified timestamp
        if (!file_exists($path)) {
            throw new KrakenRuntimeException(
                sprintf('.env file not found at: %s', $path)
            );
        }

        if (!is_readable($path)) {
            throw new KrakenRuntimeException(
                sprintf('.env file is not readable: %s', $path)
            );
        }
    }

    public function load(): void
    {
        if (empty($this->cache) || $this->needsUpdate()) {
            $this->cache = $this->parseFile($this->path);
            $this->lastModified = filemtime($this->path);

            if (empty($this->cache)) {
                throw new KrakenRuntimeException('.env file is empty');
            }
        }
    }

    private function needsUpdate(): bool
    {
        return !file_exists($this->path)
            || filemtime($this->path) !== $this->lastModified;
    }

    public function get(string $key): ?string
    {
        $this->load();
        if (!isset($this->cache[$key])) {
            throw new KrakenRuntimeException(
                sprintf("Environment variable '%s' not found", $key)
            );
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
            static function ($line) {
                return !str_starts_with(trim($line), '#');
            }
        );

        $data = [];
        foreach ($lines as $line) {
            [$key, $value] = explode('=', $line, 2);
            $data[trim($key)] = trim($value);
        }
        return $data;
    }

}
