<?php

declare(strict_types=1);

namespace Tests;

use Ludens\Routing\Router;
use Ludens\Routing\Support\Handler;
use PHPUnit\Framework\TestCase;
use Tests\Fixtures\FakeController;

final class RouterTest extends TestCase
{
    public function testValidStringReturned(): void
    {
        $handler = new Handler(FakeController::class, 'index');
        $routes = ['/' => $handler];
        $this->expectOutputString('index called');
        Router::run($routes, '/');
    }

    public function testValidStringWithArgumentReturned(): void
    {
        $handler = new Handler(FakeController::class, 'withArgument');
        $routes = ['/user/{username}' => $handler];
        $this->expectOutputString('hello quentin');
        Router::run($routes, '/user/quentin');
    }

    public function testValidStringWithMultipleArgumentsReturned(): void
    {
        $handler = new Handler(FakeController::class, 'withMultipleArguments');
        $routes = ['/posts/{category}/{id}' => $handler];
        $this->expectOutputString('php:8');
        Router::run($routes, '/posts/php/8');
    }
}
