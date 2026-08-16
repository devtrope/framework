<?php

declare(strict_types=1);

namespace Tests\Fixtures\Services;

final class ServiceWithBoundValue
{
    public function __construct(private string $apiKey)
    {
    }

    public function getApiKey(): string
    {
        return $this->apiKey;
    }
}
