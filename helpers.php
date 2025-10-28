<?php

if (! function_exists('flash')) {
    function flash(string $key): ?string
    {
        return \Ludens\Http\Support\SessionFlash::getInstance()->flash($key);
    }
}

if (! function_exists('old')) {
    function old(string $field): ?string
    {
        return \Ludens\Http\Support\SessionFlash::getInstance()->oldData($field);
    }
}

if (! function_exists('error')) {
    function error(string $field): ?string
    {
        return \Ludens\Http\Support\SessionFlash::getInstance()->error($field);
    }
}

if (! function_exists('hasError')) {
    function hasError(string $field): bool
    {
        return \Ludens\Http\Support\SessionFlash::getInstance()->hasError($field);
    }
}

if (! function_exists('hasFlash')) {
    function hasFlash(string $key): bool
    {
        return \Ludens\Http\Support\SessionFlash::getInstance()->hasFlash($key);
    }
}
