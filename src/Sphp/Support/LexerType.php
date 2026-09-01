<?php

namespace Ludens\Sphp\Support;

enum LexerType
{
    case IDENTIFIER;
    case STRING;
    case NUMBER;
    case BOOLEAN;
    case COLON;
    case INDENTATION;
    case EOF;
}
