<?php

declare(strict_types=1);

namespace Tests;

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
        $routes = ['/' => [FakeController::class, 'index']];
        $resolvedRoute = $this->resolver->resolve($routes, '/');

        [$controller, $method] = $resolvedRoute->getHandler();

        $this->assertInstanceOf(FakeController::class, $controller);
        $this->assertSame('index', $method);
        $this->assertSame([], $resolvedRoute->getParameters());
    }

    public function testResolvesDynamicRouteWithArgument(): void
    {
        $routes = ['/user/{username}' => [FakeController::class, 'withArgument']];
        $resolvedRoute = $this->resolver->resolve($routes, '/user/quentin');

        [$controller, $method] = $resolvedRoute->getHandler();

        $this->assertInstanceOf(FakeController::class, $controller);
        $this->assertSame('withArgument', $method);
        $this->assertSame(['username' => 'quentin'], $resolvedRoute->getParameters());
    }

    public function testResolvesDynamicRouteWithMultipleArguments(): void
    {
        $routes = ['/posts/{category}/{id}' => [FakeController::class, 'withMultipleArguments']];
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

    public function testDoesNotMatchWhenSegmentCountDiffers(): void
    {
        $routes = ['/user/{username}/profile' => [FakeController::class, 'withArgument']];

        $this->expectException(RouteNotFoundException::class);
        $this->resolver->resolve($routes, '/user/quentin');
    }
}
