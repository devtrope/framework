<?php

declare(strict_types=1);

namespace Tests;

use Ludens\Exceptions\InvalidControllerException;
use Ludens\Exceptions\InvalidMethodException;
use Ludens\Exceptions\RouteNotFoundException;
use Ludens\Routing\RouteResolver;
use Ludens\Routing\Support\Handler;
use PHPUnit\Framework\TestCase;
use Tests\Fixtures\FakeController;

final class RouteResolverTest extends TestCase
{
    private RouteResolver $resolver;

    protected function setUp(): void
    {
        $this->resolver = new RouteResolver();
    }

    public function testResolvesStaticRoute(): void
    {
        $handler = new Handler(FakeController::class, 'index');
        $routes = ['/' => $handler];
        $resolvedRoute = $this->resolver->resolve($routes, '/');

        [$controller, $method] = $resolvedRoute->getHandler();

        $this->assertInstanceOf(FakeController::class, $controller);
        $this->assertSame('index', $method);
        $this->assertSame([], $resolvedRoute->getParameters());
    }

    public function testResolvesDynamicRouteWithArgument(): void
    {
        $handler = new Handler(FakeController::class, 'withArgument');
        $routes = ['/user/{username}' => $handler];
        $resolvedRoute = $this->resolver->resolve($routes, '/user/quentin');

        [$controller, $method] = $resolvedRoute->getHandler();

        $this->assertInstanceOf(FakeController::class, $controller);
        $this->assertSame('withArgument', $method);
        $this->assertSame(['username' => 'quentin'], $resolvedRoute->getParameters());
    }

    public function testResolvesDynamicRouteWithMultipleArguments(): void
    {
        $handler = new Handler(FakeController::class, 'withMultipleArguments');
        $routes = ['/posts/{category}/{id}' => $handler];
        $resolvedRoute = $this->resolver->resolve($routes, '/posts/php/8');

        [$controller, $method] = $resolvedRoute->getHandler();

        $this->assertInstanceOf(FakeController::class, $controller);
        $this->assertSame('withMultipleArguments', $method);
        $this->assertSame(['category' => 'php', 'id' => '8'], $resolvedRoute->getParameters());
    }

    public function testThrowsWhenNoRouteMatches(): void
    {
        $this->expectException(RouteNotFoundException::class);
        $this->resolver->resolve([], '/unknown');
    }

    public function testThrowsWhenControllerDoesNotExist(): void
    {
        $this->expectException(InvalidControllerException::class);
        $handler = new Handler('JustANonExistingController', 'index');
        $routes = ['/contact' => $handler];

        $this->resolver->resolve($routes, '/contact');
    }

    public function testThrowsWhenMethodDoesNotExist(): void
    {
        $this->expectException(InvalidMethodException::class);
        $handler = new Handler(FakeController::class, 'contact');
        $routes = ['/contact' => $handler];

        $this->resolver->resolve($routes, '/contact');
    }

    public function testDoesNotMatchWhenSegmentCountDiffers(): void
    {
        $handler = new Handler(FakeController::class, 'withArgument');
        $routes = ['/user/{username}/profile' => $handler];

        $this->expectException(RouteNotFoundException::class);
        $this->resolver->resolve($routes, '/user/quentin');
    }
}
