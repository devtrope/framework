<?php

namespace Ludens\Sphp;

use Ludens\Sphp\LexerType;

final class Lexer
{
    private const COLON = ':';
    private const QUOTE = '\'';
    private array $content = [];
    private array $tokens = [];
    private int $i = 0;
    
    public function tokenize(string $content): array
    {
        $this->content = str_split($content);
        while ($this->i < \count($this->content)) {
            if (ctype_alpha($this->content[$this->i])) {
                $this->handleIdentifier();
            }
            
            if (self::QUOTE === $this->content[$this->i]) {
                $this->handleString();
            }

            if (ctype_digit($this->content[$this->i])) {
                $this->handleNumber();
            }

            if (self::COLON === $this->content[$this->i]) {
                $this->append(LexerType::COLON, self::COLON);
            }

            $this->i++;
        }

        return $this->tokens;
    }

    private function handleIdentifier(): void
    {
        $value = null;
        while (ctype_alpha($this->content[$this->i])) {
            $value .= $this->content[$this->i];
            $this->i++;
        }
        $this->append(LexerType::IDENTIFIER, $value);
    }

    private function handleString(): void
    {
        // A string type starts with a quote, so we want to move forward
        // to store the real string value
        $this->i++;
        $value = null;
        while (self::QUOTE !== $this->content[$this->i]) {
            $value .= $this->content[$this->i];
            $this->i++;
        }
        $this->append(LexerType::STRING, $value);
    }

    private function handleNumber(): void
    {
        $value = null;
        while (ctype_digit($this->content[$this->i]) || '.' === $this->content[$this->i]) {
            $value .= $this->content[$this->i];
            $this->i++;
        }
        $this->append(LexerType::NUMBER, $value);
    }

    private function append(LexerType $type, string $value)
    {
        $this->tokens[] = [$type->value => $value];
    }
}
