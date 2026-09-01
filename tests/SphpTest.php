<?php

declare(strict_types=1);

namespace Tests;

use Ludens\Sphp\Sphp;
use PHPUnit\Framework\TestCase;

final class SphpTest extends TestCase
{
    public function testParsesAFlatArrayAtASingleLevel(): void
    {
        $sphp = new Sphp();
        $result = $sphp->parse(__DIR__ . '/Fixtures/Config/flat-array.sphp');
        $this->assertSame([
            'services' => [
                'version' => 1.4,
                'debug' => 'true'
            ],
        ], $result);
    }

    public function testParsesTwoNestedLevelsWithoutBacktracking(): void
    {
        $sphp = new Sphp();
        $result = $sphp->parse(__DIR__ . '/Fixtures/Config/nested-two-levels.sphp');
        $this->assertSame([
            'root' => [
                'A' => [
                    'B' => [
                        'x' => 1,
                    ],
                ],
            ],
        ], $result);
    }

    public function testParsesASiblingKeyAfterANestedArrayCloses(): void
    {
        $sphp = new Sphp();
        $result = $sphp->parse(__DIR__ . '/Fixtures/Config/sibling-after-nested.sphp');
        $this->assertSame([
            'services' => [
                'A' => [
                    'x' => 1,
                    'y' => 2,
                ],
                'B' => 3,
            ],
        ], $result);
    }
}
