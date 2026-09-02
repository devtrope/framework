<?php

declare(strict_types=1);

namespace Tests;

use Ludens\Exceptions\InvalidControllerFolderException;
use Ludens\Http\Support\HttpMethod;
use Ludens\Routing\MethodAttributesResolver;
use Ludens\Routing\Route;
use Ludens\Routing\RoutesRegisterer;
use PHPUnit\Framework\TestCase;

final class RoutesRegistererTest extends TestCase
{
    private MethodAttributesResolver $methodAttributesResolver;

    public function setUp(): void
    {
        Route::reset();
        $this->methodAttributesResolver = new MethodAttributesResolver();
    }

    public function testRegisterRoutesFromControllerMethodAttributes(): void
    {
        $this->createRegisterer()->register();

        $this->assertArrayHasKey('/fixture', Route::getAllByRequestMethod(HttpMethod::GET));
        $this->assertArrayHasKey('/fixture/create', Route::getAllByRequestMethod(HttpMethod::POST));
    }

    public function testIgnoresMethodsWithoutHttpMethodAttribute(): void
    {
        $this->createRegisterer()->register();

        $this->assertCount(1, Route::getAllByRequestMethod(HttpMethod::GET));
        $this->assertCount(1, Route::getAllByRequestMethod(HttpMethod::POST));
    }

    public function testThrowsWhenControllerFolderDoesNotExist(): void
    {
        $registerer = $this->createRegisterer(controllerFolder: __DIR__ . '/Fixtures/DoesNotExist/');

        $this->expectException(InvalidControllerFolderException::class);

        $registerer->register();
    }

    private function createRegisterer(
        string $controllerFolder = __DIR__ . '/Fixtures/Controller/',
        string $controllerNamespace = '\\Tests\\Fixtures\\Controller\\'
    ): RoutesRegisterer {
        return new RoutesRegisterer($this->methodAttributesResolver, $controllerFolder, $controllerNamespace);
    }
}
