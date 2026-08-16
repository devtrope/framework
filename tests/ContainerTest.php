<?php

declare(strict_types=1);

namespace Tests;

use Ludens\DependencyInjection\Container;
use Ludens\Exceptions\InvalidConfigurationFileProvided;
use Ludens\Exceptions\MissingBoundValueException;
use PHPUnit\Framework\TestCase;
use Tests\Fixtures\Services\Application;
use Tests\Fixtures\Services\Logger;
use Tests\Fixtures\Services\Mailer;
use Tests\Fixtures\Services\ServiceWithBoundValue;
use Tests\Fixtures\Services\ServiceWithDefaultValue;

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

    public function testUsesTheParameterDefaultValueWhenTypeIsNotAClass(): void
    {
        $service = $this->container->get(ServiceWithDefaultValue::class);
        $this->assertSame('production', $service->getEnvironment());
    }

    public function testUsesABoundValueWhenNoDefaultIsAvailable(): void
    {
        $this->container->load(__DIR__ . '/Fixtures/Config/services.php');
        $service = $this->container->get(ServiceWithBoundValue::class);
        $this->assertSame('config-secret-key', $service->getApiKey());
    }

    public function testThrowsWhenConfigurationFileDoesNotExist(): void
    {
        $this->expectException(InvalidConfigurationFileProvided::class);
        $this->container->load(__DIR__ . '/Fixtures/Config/does-not-exist.php');
    }

    public function testThrowsWhenValueHasNoBound(): void
    {
        $this->expectException(MissingBoundValueException::class);
        $this->container->load(__DIR__ . '/Fixtures/Config/invalid-services.php');
        $this->container->get(ServiceWithBoundValue::class);
    }
}
