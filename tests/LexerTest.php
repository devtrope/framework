<?php

declare(strict_types=1);

namespace Tests;

use Ludens\Sphp\Lexer;
use Ludens\Sphp\Support\LexerType;
use Ludens\Sphp\Token\LexerToken;
use PHPUnit\Framework\TestCase;

final class LexerTest extends TestCase
{
    public function testProducesAnIndentationTokenWithDepthOfOneLevel(): void
    {
        $lexer = new Lexer("services:\n  version: 1");
        $tokenized = $lexer->tokenize();
        $indentation = $this->findFirstTokenOfType($tokenized, LexerType::INDENTATION);
        $this->assertSame(2, $indentation->getValue());
    }

    public function testProducesAnIndentationTokenWithDepthOfTwoLevels(): void
    {
        $lexer = new Lexer("services:\n  A:\n    x: 1");
        $tokenized = $lexer->tokenize();
        $indentations = $this->findAllTokensOfType($tokenized, LexerType::INDENTATION);
        $this->assertCount(2, $indentations);
        $this->assertSame(2, $indentations[0]->getValue());
        $this->assertSame(4, $indentations[1]->getValue());
    }

    public function testDoesNotProduceAnIndentationTokenOnRootLever(): void
    {
        $lexer = new Lexer("title: 'Ludens'\nauthor: 'Quentin'");
        $tokenized = $lexer->tokenize();
        $indentation = $this->findFirstTokenOfType($tokenized, LexerType::INDENTATION);
        $this->assertNull($indentation);
    }

    private function findFirstTokenOfType(array $tokens, LexerType $type): null|LexerToken
    {
        foreach ($tokens as $token) {
            if ($type === $token->getType()) {
                return $token;
            }
        }
        return null;
    }

    private function findAllTokensOfType(array $tokens, LexerType $type): array
    {
        $matching = [];
        foreach ($tokens as $token) {
            if ($type === $token->getType()) {
                $matching[] = $token;
            }
        }
        return $matching;
    }
}
