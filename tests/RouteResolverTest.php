<?php

declare(strict_types=1);

namespace Tests;

use Ludens\Exceptions\RouteNotFoundException;
use Ludens\Routing\RouteResolver;
use Ludens\Routing\Support\Handler;
use PHPUnit\Framework\TestCase;
use Tests\Fixtures\Controller\FakeController;

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

        $this->assertInstanceOf(FakeController::class, $resolvedRoute->getHandler()->getController());
        $this->assertSame('index', $resolvedRoute->getHandler()->getMethod());
        $this->assertSame([], $resolvedRoute->getParameters());
    }

    public function testResolvesDynamicRouteWithArgument(): void
    {
        $handler = new Handler(FakeController::class, 'withArgument');
        $routes = ['/user/{username}' => $handler];
        $resolvedRoute = $this->resolver->resolve($routes, '/user/quentin');

        $this->assertInstanceOf(FakeController::class, $resolvedRoute->getHandler()->getController());
        $this->assertSame('withArgument', $resolvedRoute->getHandler()->getMethod());
        $this->assertSame(['username' => 'quentin'], $resolvedRoute->getParameters());
    }

    public function testResolvesDynamicRouteWithMultipleArguments(): void
    {
        $handler = new Handler(FakeController::class, 'withMultipleArguments');
        $routes = ['/posts/{category}/{id}' => $handler];
        $resolvedRoute = $this->resolver->resolve($routes, '/posts/php/8');

        $this->assertInstanceOf(FakeController::class, $resolvedRoute->getHandler()->getController());
        $this->assertSame('withMultipleArguments', $resolvedRoute->getHandler()->getMethod());
        $this->assertSame(['category' => 'php', 'id' => '8'], $resolvedRoute->getParameters());
    }

    public function testThrowsWhenNoRouteMatches(): void
    {
        $this->expectException(RouteNotFoundException::class);
        $this->resolver->resolve([], '/unknown');
    }

    public function testDoesNotMatchWhenSegmentCountDiffers(): void
    {
        $handler = new Handler(FakeController::class, 'withArgument');
        $routes = ['/user/{username}/profile' => $handler];

        $this->expectException(RouteNotFoundException::class);
        $this->resolver->resolve($routes, '/user/quentin');
    }
}
