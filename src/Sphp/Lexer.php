<?php

namespace Ludens\Sphp;

use Ludens\Sphp\LexerType;

final class Lexer
{
    private const COLON = ":";
    private const QUOTE = "'";
    private const NEW_LINE = "\n";
    private array $content = [];
    private int $position = 0;
    private int $line = 1;

    public function tokenize(string $content): array
    {
        $tokens = [];
        $this->content = str_split($content);
        
        while ($this->position < \count($this->content)) {
            if (self::NEW_LINE === $this->content[$this->position]) {
                $this->line++;
            }

            if (ctype_alpha($this->content[$this->position])) {
                $tokens[] = $this->handleIdentifier();
            }
            
            if (self::QUOTE === $this->content[$this->position]) {
                $tokens[] = $this->handleString();
            }

            if (ctype_digit($this->content[$this->position])) {
                $tokens[] = $this->handleNumber();
            }

            if (self::COLON === $this->content[$this->position]) {
                $tokens[] = $this->append(LexerType::COLON, self::COLON);
            }

            $this->position++;
        }

        return $tokens;
    }

    private function handleIdentifier(): array
    {
        $value = null;
        while (ctype_alpha($this->content[$this->position])) {
            $value .= $this->content[$this->position];
            $this->position++;
        }
        return $this->append(LexerType::IDENTIFIER, $value);
    }

    private function handleString(): array
    {
        // A string type starts with a quote, so we want to move forward
        // to store the real string value
        $this->position++;
        $value = null;
        while (self::QUOTE !== $this->content[$this->position]) {
            $value .= $this->content[$this->position];
            $this->position++;
        }
        return $this->append(LexerType::STRING, $value);
    }

    private function handleNumber(): array
    {
        $value = null;
        while (
            null !== $this->content[$this->position] && 
            (
                ctype_digit($this->content[$this->position]) ||
                '.' === $this->content[$this->position]
            )
        ) {
            $value .= $this->content[$this->position];
            $this->position++;
        }
        return $this->append(LexerType::NUMBER, $value);
    }

    private function append(LexerType $type, string $value): array
    {
        return ['type' => $type->value, 'value' => $value, 'line' => $this->line];
    }
}
