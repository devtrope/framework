<?php

declare(strict_types=1);

namespace Tests;

use Ludens\Exceptions\InvalidControllerException;
use Ludens\Exceptions\InvalidMethodException;
use Ludens\Exceptions\RouteNotFoundException;
use Ludens\Routing\RouteResolver;
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
        $routes = ['/' => ['controller' => FakeController::class, 'method' => 'index']];
        $resolvedRoute = $this->resolver->resolve($routes, '/');

        [$controller, $method] = $resolvedRoute->getHandler();

        $this->assertInstanceOf(FakeController::class, $controller);
        $this->assertSame('index', $method);
        $this->assertSame([], $resolvedRoute->getParameters());
    }

    public function testResolvesDynamicRouteWithArgument(): void
    {
        $routes = ['/user/{username}' => ['controller' => FakeController::class, 'method' => 'withArgument']];
        $resolvedRoute = $this->resolver->resolve($routes, '/user/quentin');

        [$controller, $method] = $resolvedRoute->getHandler();

        $this->assertInstanceOf(FakeController::class, $controller);
        $this->assertSame('withArgument', $method);
        $this->assertSame(['username' => 'quentin'], $resolvedRoute->getParameters());
    }

    public function testResolvesDynamicRouteWithMultipleArguments(): void
    {
        $routes = ['/posts/{category}/{id}' => ['controller' => FakeController::class, 'method' => 'withMultipleArguments']];
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
        $routes = ['/contact' => ['controller' => 'JustANonExistingController', 'method' => 'index']];

        $this->expectException(InvalidControllerException::class);
        $this->resolver->resolve($routes, '/contact');
    }

    public function testThrowsWhenMethodDoesNotExist(): void
    {
        $routes = ['/contact' => ['controller' => FakeController::class, 'method' => 'contact']];

        $this->expectException(InvalidMethodException::class);
        $this->resolver->resolve($routes, '/contact');
    }

    public function testDoesNotMatchWhenSegmentCountDiffers(): void
    {
        $routes = ['/user/{username}/profile' => ['controller' => FakeController::class, 'method' => 'withArgument']];

        $this->expectException(RouteNotFoundException::class);
        $this->resolver->resolve($routes, '/user/quentin');
    }
}
