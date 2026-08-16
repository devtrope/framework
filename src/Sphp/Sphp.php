<?php

namespace Ludens\Sphp;

final class Sphp
{
    private const KEY_VALUE_REGEX = '/^([\w-]+)\s*:\s*(.+)$/';
    private const DOUBLE_QUOTE_REGEX = '/^"(.*)"$/';
    private const SIMPLE_QUOTE_REGEX = '/^\'(.*)\'$/';
    private const INT_REGEX = '/^-?\d+$/';
    private const FLOAT_REGEX = '/^-?\d+\.\d+$/';
    private array $result = [];

    public function parse(string $filepath): array
    {
        $lines = file($filepath, FILE_IGNORE_NEW_LINES);
        foreach ($lines as $lineNumber => $line) {
            $this->parseLine($line, $lineNumber + 1);
        }
        return $this->result;
    }

    private function parseLine(string $line, int $lineNumber): void
    {
        $trimmed = trim($line);

        // Ignore empty lines and comments
        if ('' === $trimmed || str_starts_with($trimmed, '#')) {
            return;
        }

        // Key: Value
        if (preg_match(self::KEY_VALUE_REGEX, $trimmed, $matches)) {
            [$result, $key, $value] = $matches;
            $this->setValue($key, $this->castValue($value));
            return;
        }
    }

    private function castValue(mixed $value): mixed
    {
        $raw = trim($value);

        if (preg_match(self::DOUBLE_QUOTE_REGEX, $raw, $m)) {
            return $m[1];
        }

        if (preg_match(self::SIMPLE_QUOTE_REGEX, $raw, $m)) {
            return $m[1];
        }

        if ('true' === $raw) {
            return true;
        }

        if ('false' === $raw) {
            return false;
        }

        if (preg_match(self::INT_REGEX, $raw)) {
            return (int) $raw;
        }

        if (preg_match(self::FLOAT_REGEX, $raw)) {
            return (float) $raw;
        }

        return $raw;
    }

    private function setValue(string $key, mixed $value)
    {
        $this->result[$key] = $value;
    }
}
