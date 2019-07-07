<?php

namespace App\Tests\Twig;

use App\Twig\AppExtension;
use PHPUnit\Framework\TestCase;
use Twig\Environment;

class AppExtensionTest extends TestCase
{
    /**
     * @dataProvider rdsProvider
     */
    public function testAlignRdsFrame(string $frame, string $expectedFrame): void
    {
        $appExtension = $this->getInstance();

        $this->assertEquals($expectedFrame, $appExtension->alignRDSFrame($frame));
    }

    public function rdsProvider(): array
    {
        return [
            ['POLSKIE', 'POLSKIE '],
            [' RADIO',  '  RADIO '],
            ['JEDYNKA', 'JEDYNKA '],
            [' 20:00',  '  20:00 '],

            ['x',         '   x    '],
            ['xx',        '   xx   '],
            ['xxx',       '  xxx   '],
            ['xxxx',      '  xxxx  '],
            ['xxxxx',     ' xxxxx  '],
            ['xxxxxx',    ' xxxxxx '],
            ['xxxxxxx',   'xxxxxxx '],
            ['xxxxxxxx',  'xxxxxxxx'],
            ['xxxxxxxxx', 'xxxxxxxx'],
        ];
    }

    /**
     * @dataProvider csvProvider
     */
    public function testCsvEscaper(string $sourceString, string $escapedString): void
    {
        $appExtension = $this->getInstance();
        $twigEnvironment = $this->createMock(Environment::class);

        $this->assertEquals(
            $escapedString,
            $appExtension->escapeCSV($twigEnvironment, $sourceString)
        );
    }

    public function csvProvider(): array
    {
        return [
            [
                'something',
                'something',
            ],
            [
                'something with space',
                '"something with space"',
            ],
            [
                "something\nwith newline",
                "\"something\nwith newline\"",
            ],
        ];
    }

    private function getInstance(): AppExtension
    {
        $reflection = new \ReflectionClass(AppExtension::class);

        return $reflection->newInstanceWithoutConstructor();
    }
}
