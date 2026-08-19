<?php

namespace Ludens\Sphp;

use Ludens\Sphp\LexerType;

final class Lexer
{
    private const COLON = ":";
    private const QUOTE = "'";
    private const NEW_LINE = "\n";
    private int $position = 0;
    private int $line = 1;

    public function __construct(
        private string $input
    ) {}

    public function tokenize(): array
    {
        $tokens = [];

        while ($this->position < \strlen($this->input)) {
            $character = $this->input[$this->position];

            if (self::NEW_LINE === $character) {
                $this->line++;
                $this->position++;
                continue;
            }

            if (ctype_alpha($character)) {
                $tokens[] = $this->readIdentifier();
                continue;
            }
            
            if (ctype_digit($character)) {
                $tokens[] = $this->readNumber();
                continue;
            }

            if (self::QUOTE === $character) {
                $tokens[] = $this->readString();
                //Skip the last quote closing the string value
                $this->position++;
                continue;
            }

            if (self::COLON === $character) {
                $tokens[] = $this->append(LexerType::COLON, self::COLON);
                $this->position++;
                continue;
            }

            $this->position++;
        }

        return $tokens;
    }

    private function readIdentifier(): array
    {
        $value = null;
        while (
            $this->position < \strlen($this->input) &&
            ctype_alpha($this->input[$this->position])
        ) {
            $value .= $this->input[$this->position];
            $this->position++;
        }
        return $this->append(LexerType::IDENTIFIER, $value);
    }

    private function readNumber(): array
    {
        $value = null;
        while (
            $this->position < \strlen($this->input) &&
            null !== $this->input[$this->position] && 
            (
                ctype_digit($this->input[$this->position]) ||
                '.' === $this->input[$this->position]
            )
        ) {
            $value .= $this->input[$this->position];
            $this->position++;
        }
        return $this->append(LexerType::NUMBER, $value);
    }

    private function readString(): array
    {
        // A string type starts with a quote, so we want to move forward
        // to store the real string value
        $this->position++;
        $value = null;
        while (
            $this->position < \strlen($this->input) &&
            self::QUOTE !== $this->input[$this->position]
        ) {
            $value .= $this->input[$this->position];
            $this->position++;
        }
        return $this->append(LexerType::STRING, $value);
    }

    private function append(LexerType $type, string $value): array
    {
        return ['type' => $type->name, 'value' => $value, 'line' => $this->line];
    }
}
