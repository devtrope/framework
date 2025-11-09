<?php

namespace Ludens\Database;

class ModelManager
{
    public function get(string $class): object
    {
        return new $class();
    }
}
