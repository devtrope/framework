<?php

namespace Ludens\Http\Support;

enum HttpResponseCode: int
{
    case OK        = 200;
    case NOT_FOUND = 404;
}
