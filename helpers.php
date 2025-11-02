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

if (! function_exists('env')) {
    function env(string $key, $default = null)
    {
        $value = $_ENV[$key];

        if (! $value) {
            return $default;
        }

        if ($value === 'true') {
            return true;
        }

        if ($value === 'false') {
            return false;
        }

        return $value;
    }
}

if (! function_exists('dd')) {
    function dd(mixed ...$vars) {
        echo '<pre style="background: #1e1e1e; color: #d4d4d4; padding: 20px; border-radius: 5px; font-family: monospace;">';

        foreach ($vars as $var) {
            var_dump($var);
            echo "\n\n";
        }

        echo '</pre>';
        die(1);
    }
}