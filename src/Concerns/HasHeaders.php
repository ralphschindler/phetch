<?php

namespace Phetch\Concerns;

trait HasHeaders
{
    protected function normalizeHeaders(array $headers): array
    {
        return array_column(array_map(function ($name, $value) {
            return [$this->normalizeHeaderName($name), $value];
        }, array_keys($headers), $headers), 1, 0);
    }

    protected function normalizeHeaderName(string $name): string
    {
        return str_replace(' ', '-', ucwords(str_replace(['-', '_'], ' ', $name)));
    }
}

