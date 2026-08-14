<?php

declare(strict_types=1);

namespace Tests\Fixtures\Controller;

use Ludens\Routing\Routes;

final class FixtureController
{
    #[Routes\Get('/fixture')]
    public function index(): void
    {
    }

    #[Routes\Post('/fixture/create')]
    public function create(): void
    {
    }

    public function notARoute(): void
    {
    }
}