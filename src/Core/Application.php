<?php

namespace Ludens\Core;

class Application
{
    public static function init(\Ludens\Http\Request $request)
    {
        \Ludens\Routing\Router::dispatch($request);
    }
}