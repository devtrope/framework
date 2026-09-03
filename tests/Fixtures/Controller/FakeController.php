<?php

declare(strict_types=1);

namespace Tests\Fixtures\Controller;

use Ludens\Http\Response;

final class FakeController
{
    public function index(): Response
    {
        $response = new Response();
        return $response->setBody("index called");
    }

    public function withArgument(string $username): Response
    {
        $response = new Response();
        return $response->setBody("hello {$username}");
    }

    public function withMultipleArguments(string $category, string $id): Response
    {
        $response = new Response();
        return $response->setBody("{$category}:{$id}");
    }
}
