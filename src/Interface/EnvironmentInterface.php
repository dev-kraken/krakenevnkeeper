<?php

namespace KrakenInterface;

interface EnvironmentInterface
{
    public function get(string $key): ?string;

    public function has(string $key): bool;
}