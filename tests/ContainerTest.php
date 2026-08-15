<?php

declare(strict_types=1);

namespace Tests;

use Ludens\DependencyInjection\Container;
use PHPUnit\Framework\TestCase;
use Tests\Fixtures\Services\Application;
use Tests\Fixtures\Services\Logger;
use Tests\Fixtures\Services\Mailer;

final class ContainerTest extends TestCase
{
    private Container $container;

    public function setUp(): void
    {
        $this->container = new Container();
    }

    public function testResolvesAClassWithoutConstructorDependencies(): void
    {
        $mailer = $this->container->get(Mailer::class);
        $this->assertInstanceOf(Mailer::class, $mailer);
    }

    public function testResolvesAClassWithASingleDependency(): void
    {
        $logger = $this->container->get(Logger::class);
        $this->assertInstanceOf(Logger::class, $logger);
    }

    public function testResolvesNestedDependenciesRecursively(): void
    {
        $application = $this->container->get(Application::class);
        $this->assertInstanceOf(Application::class, $application);
    }
}
