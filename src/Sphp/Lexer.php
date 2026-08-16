<?php

namespace Ludens\Sphp;

final class Lexer
{
    public function tokenize(string $content): array
    {
        $tokens = [];
        $content = str_split($content);
        for ($i = 0; $i < \count($content); $i++) {
            if (ctype_alpha($content[$i])) {
                $value = null;
                while (ctype_alpha($content[$i])) {
                    $value .= $content[$i];
                    $i++;
                }
                $tokens[] = ['IDENTIFIER' => $value];
            }

            if (':' === $content[$i]) {
                $tokens[] = ['COLON' => ':'];
            }

            if ('\'' === $content[$i]) {
                $i++;
                $value = null;
                while ('\'' !== $content[$i]) {
                    $value .= $content[$i];
                    $i++;
                }
                $tokens[] = ['STRING' => $value];
            }

            if (ctype_digit($content[$i])) {
                $value = null;
                while(ctype_digit($content[$i]) || '.' === $content[$i]) {
                    $value .= $content[$i];
                    $i++;
                }
                $tokens[] = ['NUMBER' => $value];
            }
        }

        return $tokens;
    }
}
