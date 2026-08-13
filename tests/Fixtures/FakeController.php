<?php

declare(strict_types=1);

namespace Tests\Fixtures;

final class FakeController
{
    public function index(): void
    {
        echo "index called\n";
    }

    public function withArgument(string $username): void
    {
        echo "hello {$username}\n";
    }

    public function withMultipleArguments(string $category, string $id): void
    {
        echo "{$category}:{$id}\n";
    }
}
