<?php

declare(strict_types=1);

namespace Tests\Fixtures\Services;

final class ServiceWithDefaultValue
{
    public function __construct(private string $environment = 'production')
    {
    }

    public function getEnvironment(): string
    {
        return $this->environment;
    }
}
