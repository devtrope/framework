<?php

declare(strict_types=1);

namespace Tests\Fixtures\Services;

final class Application
{
    public function __construct(private Logger $logger) {}
}
