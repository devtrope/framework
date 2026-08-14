<?php

declare(strict_types=1);

namespace Tests;

use Ludens\Exceptions\InvalidControllerFolderException;
use Ludens\Routing\Route;
use Ludens\Routing\RoutesRegisterer;
use PHPUnit\Framework\TestCase;

final class RoutesRegistererTest extends TestCase
{
    public function testRegisterRoutesFromControllerMethodAttributes(): void
    {
        $registerer = new RoutesRegisterer(
            controllerFolder: 'tests/Fixtures/Controller/',
            controllerNamespace: '\\Tests\\Fixtures\\Controller\\'
        );

        $registerer->register();

        $this->assertArrayHasKey('/fixture', Route::getAllByRequestMethod('GET'));
        $this->assertArrayHasKey('/fixture/create', Route::getAllByRequestMethod('POST'));
    }

    public function testIgnoresMethodsWithoutHttpMethodAttribute(): void
    {
        $registerer = new RoutesRegisterer(
            controllerFolder: 'tests/Fixtures/Controller/',
            controllerNamespace: '\\Tests\\Fixtures\\Controller\\'
        );

        $registerer->register();

        $this->assertCount(1, Route::getAllByRequestMethod('GET'));
        $this->assertCount(1, Route::getAllByRequestMethod('POST'));
    }

    public function testThrowsWhenControllerFolderDoesNotExist(): void
    {
        $registerer = new RoutesRegisterer(
            controllerFolder: __DIR__ . '/Fixtures/DoesNotExist/'
        );

        $this->expectException(InvalidControllerFolderException::class);

        $registerer->register();
    }
}
