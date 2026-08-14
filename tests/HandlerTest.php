<?php

declare(strict_types=1);

namespace Tests;

use Ludens\Exceptions\InvalidControllerException;
use Ludens\Exceptions\InvalidMethodException;
use Ludens\Routing\Support\Handler;
use PHPUnit\Framework\TestCase;
use Tests\Fixtures\Controller\FakeController;

final class HandlerTest extends TestCase
{
    public function testThrowsWhenControllerDoesNotExist(): void
    {
        $this->expectException(InvalidControllerException::class);
        new Handler('JustANonExistingController', 'index');
    }

    public function testThrowsWhenMethodDoesNotExist(): void
    {
        $this->expectException(InvalidMethodException::class);
        new Handler(FakeController::class, 'contact');
    }
}
