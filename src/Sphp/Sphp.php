<?php

namespace Ludens\Sphp;

use Ludens\Exceptions\ConfigurationFormatException;
use Ludens\Exceptions\InvalidConfigurationFileProvided;
use Ludens\Sphp\Support\LexerType;
use Ludens\Sphp\Token\LexerToken;
use UnexpectedValueException;

final class Sphp
{
    /**
     * @var LexerToken[]
     */
    private array $tokens = [];

    /**
     * @var int
     */
    private int $position = 0;

    /**
     * @param string $filepath
     * @throws InvalidConfigurationFileProvided
     * @return array<string, mixed>
     */
    public function parse(string $filepath): array
    {
        $result = [];
        if (false === $content = file_get_contents($filepath)) {
            throw new InvalidConfigurationFileProvided(\sprintf(
                'Cannot access %s content',
                $filepath
            ));
        }
        $lexer = new Lexer($content);
        $this->tokens = $lexer->tokenize();

        while (
            $this->position < \count($this->tokens) &&
            LexerType::EOF !== $this->peek()->getType()
        ) {
            [$identifier, $value] = $this->parseEntry();
            $result[$identifier] = $value;
        }
        return $result;
    }

    /**
     * @return array{0: string, 1: mixed}
     */
    private function parseEntry(): array
    {
        $this->expect(LexerType::IDENTIFIER);
        $identifier = $this->consume()->getValue();
        if (false === \is_string($identifier)) {
            throw new UnexpectedValueException(\sprintf(
                'Cannot convert identifier of type %s to string.',
                get_debug_type($identifier),
            ));
        }
        $this->expect(LexerType::COLON);
        $this->consume();
        
        return [$identifier, $this->parseValue()];
    }

    /**
     * @return mixed
     */
    private function parseValue(): mixed
    {
        if (LexerType::INDENTATION === $this->peek()->getType()) {
            return $this->parseArray();
        }
        return $this->handleValues();
    }

    /**
     * @return array<string, mixed>
     */
    private function parseArray(): array
    {
        $this->expect(LexerType::INDENTATION);
        $indentation = $this->consume()->getValue();
        $result = [];

        [$identifier, $value] = $this->parseEntry();
        $result[$identifier] = $value;

        /**
         * Only continue this array while the following entries are at
         * the same depth. Anything deeper or shallower is not ours.
         */
        while (
            LexerType::INDENTATION === $this->peek()->getType() &&
            $indentation === $this->peek()->getValue()
        ) {
            $this->consume();
            [$identifier, $value] = $this->parseEntry();
            $result[$identifier] = $value;
        }

        return $result;
    }

    /**
     * @return mixed
     */
    private function handleValues(): mixed
    {
        $expectedTypes = [LexerType::BOOLEAN, LexerType::STRING, LexerType::NULL, LexerType::NUMBER];
        if (false === \in_array($this->peek()->getType(), $expectedTypes)) {
            /**
             * We just throw a "random" (not so random) configuration exception, because at this point we know
             * that the token type is invalid, so any expect called with one of the expected types will trigger
             * an exception.
             */
            $this->expect(LexerType::STRING);
        }
        return $this->consume()->getValue();
    }

    /**
     * @param LexerType $expected
     * @throws ConfigurationFormatException
     * @return void
     */
    private function expect(LexerType $expected): void
    {
        if ($expected !== $this->peek()->getType()) {
            throw new ConfigurationFormatException(\sprintf(
                'Invalid token on line %s',
                $this->peek()->getLine()
            ));
        }
    }

    /**
     * @return LexerToken
     */
    private function consume(): LexerToken
    {
        $token = $this->tokens[$this->position];
        $this->position++;
        return $token;
    }

    /**
     * @return LexerToken
     */
    private function peek(): LexerToken
    {
        return $this->tokens[$this->position];
    }
}
