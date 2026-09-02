<?php

declare(strict_types=1);

namespace Tests;

use Ludens\Http\Request;
use Ludens\Routing\Route;
use Ludens\Routing\Router;
use Ludens\Routing\Support\Handler;
use Override;
use PHPUnit\Framework\TestCase;
use Tests\Fixtures\Controller\FakeController;

final class RouterTest extends TestCase
{
    #[Override]
    public function setUp(): void
    {
        Route::reset();
    }
    public function testValidStringReturned(): void
    {
        $handler = new Handler(FakeController::class, 'index');
        Route::add('GET', '/', $handler);
        $this->expectOutputString('index called');
        $request = new Request('GET', '/');
        Router::run($request);
    }

    public function testValidStringWithArgumentReturned(): void
    {
        $handler = new Handler(FakeController::class, 'withArgument');
        Route::add('GET', '/user/{username}', $handler);
        $this->expectOutputString('hello quentin');
        $request = new Request('GET', '/user/quentin');
        Router::run($request);
    }

    public function testValidStringWithMultipleArgumentsReturned(): void
    {
        $handler = new Handler(FakeController::class, 'withMultipleArguments');
        Route::add('GET', '/posts/{category}/{id}', $handler);
        $this->expectOutputString('php:8');
        $request = new Request('GET', '/posts/php/8');
        Router::run($request);
    }
}
