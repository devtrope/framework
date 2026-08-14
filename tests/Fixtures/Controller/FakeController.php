<?php

declare(strict_types=1);

namespace Tests\Fixtures\Controller;

final class FakeController
{
    public function index(): string
    {
        return "index called";
    }

    public function withArgument(string $username): string
    {
        return "hello {$username}";
    }

    public function withMultipleArguments(string $category, string $id): string
    {
        return "{$category}:{$id}";
    }
}
