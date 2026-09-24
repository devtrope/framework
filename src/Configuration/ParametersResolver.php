<?php

namespace Ludens\Configuration;

use Ludens\Core\Kernel;

final class ParametersResolver
{
    public function resolve(string $parameter): string
    {
        if (false === stripos($parameter, '%')) {
            return $parameter;
        }

        return Kernel::getInstance()->get(str_replace('%', '', $parameter));
    }
}
