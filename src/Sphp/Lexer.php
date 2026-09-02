<?php

namespace Ludens\Sphp;

use Ludens\Sphp\Support\Grammar;
use Ludens\Sphp\Token;

final class Lexer
{
    private const int MINIMUM_INDENTATION_WIDTH = 2;
    private int $position = 0;
    private int $line = 1;

    public function __construct(private string $input)
    {
    }

    public function tokenize(): array
    {
        $tokens = [];

        while ($this->position < \strlen($this->input)) {
            $character = $this->input[$this->position];

            if (Grammar::NEW_LINE === $character) {
                $this->line++;
                $this->position++;
                continue;
            }

            if (ctype_space($character)) {
                $spaces = 0;
                while (ctype_space($this->input[$this->position])) {
                    $spaces++;
                    $this->position++;
                }

                if ($spaces >= self::MINIMUM_INDENTATION_WIDTH) {
                    $tokens[] = new Token\IndentationToken($spaces, $this->line);
                }
                
                continue;
            }

            if (ctype_alpha($character) || Grammar::BACKSLASH === $character) {
                $tokens[] = $this->readIdentifier();
                continue;
            }
            
            if (ctype_digit($character)) {
                $tokens[] = $this->readNumber();
                continue;
            }

            if (Grammar::QUOTE === $character) {
                $tokens[] = $this->readString();
                //Skip the last quote closing the string value
                $this->position++;
                continue;
            }

            if (Grammar::COLON === $character) {
                $tokens[] = new Token\ColonToken($this->line);
                $this->position++;
                continue;
            }

            $this->position++;
        }

        $tokens[] = new Token\EndOfFileToken($this->line);
        return $tokens;
    }

    private function readIdentifier(): Token\LexerToken
    {
        $value = null;
        while (
            $this->position < \strlen($this->input) &&
            (
                ctype_alpha($this->input[$this->position]) ||
                Grammar::BACKSLASH === $this->input[$this->position]
            )
        ) {
            $value .= $this->input[$this->position];
            $this->position++;
        }

        if (\in_array($value, Grammar::RESERVED_KEYWORDS)) {
            return $this->readKeyword($value);
        }
        return new Token\IdentifierToken($value, $this->line);
    }

    private function readNumber(): Token\LexerToken
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
        return new Token\NumberToken($value, $this->line);
    }

    private function readString(): Token\LexerToken
    {
        // A string type starts with a quote, so we want to move forward
        // to store the real string value
        $this->position++;
        $value = null;
        while (
            $this->position < \strlen($this->input) &&
            Grammar::QUOTE !== $this->input[$this->position]
        ) {
            $value .= $this->input[$this->position];
            $this->position++;
        }
        return new Token\StringToken($value, $this->line);
    }

    private function readKeyword(string $value): Token\LexerToken
    {
        if ('null' === $value) {
            return new Token\NullToken($this->line);
        }
        return new Token\BooleanToken($value, $this->line);
    }
}
