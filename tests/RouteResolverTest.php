<?php

declare(strict_types=1);

namespace Tests;

use Exception;
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
        [$handler, $arguments] = $this->resolver->resolve($routes, '/');

        $this->assertSame([FakeController::class, 'index'], $handler);
        $this->assertSame([], $arguments);
    }

    public function testResolvesDynamicRouteWithArgument(): void
    {
        $routes = ['/user/{username}' => [FakeController::class, 'withArgument']];
        [$handler, $arguments] = $this->resolver->resolve($routes, '/user/quentin');

        $this->assertSame([FakeController::class, 'withArgument'], $handler);
        $this->assertSame(['username' => 'quentin'], $arguments);
    }

    public function testResolvesDynamicRouteWithMultipleArguments(): void
    {
        $routes = ['/posts/{category}/{id}' => [FakeController::class, 'withMultipleArguments']];
        [$handler, $arguments] = $this->resolver->resolve($routes, '/posts/php/8');

        $this->assertSame([FakeController::class, 'withMultipleArguments'], $handler);
        $this->assertSame(['category' => 'php', 'id' => '8'], $arguments);
    }

    public function testThrowsWhenNoRouteMatches(): void
    {
        $this->expectException(Exception::class);
        $this->resolver->resolve([], '/unknown');
    }

    public function testDoesNotMatchWhenSegmentCountDiffers(): void
    {
        $routes = ['/user/{username}/profile' => [FakeController::class, 'withArgument']];

        $this->expectException(Exception::class);
        $this->resolver->resolve($routes, '/user/quentin');
    }
}
