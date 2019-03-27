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
    public function testFormatRdsFrames(array $frames, array $formattedFrames): void
    {
        $appExtension = $this->getInstance();

        $result = $appExtension->formatRDSFrames($frames);

        $this->assertEquals($formattedFrames, $result);

        $this->assertTrue((function($result){
            foreach ($result as $item) {
                if (8 !== mb_strlen($item)) {
                    return false;
                }
            }
            return true;
        })($result));
    }

    public function rdsProvider(): array
    {
        return [
            [
                ['POLSKIE',   '_RADIO',  'JEDYNKA',   '_20:00' ],
                ['POLSKIE ', '  RADIO ', 'JEDYNKA ', '  20:00 '],
            ],
            [
                [ 'RMF_FM',  'KATOWICE',   'NAJ-',    'LEPSZA',   'MUZYKA' ],
                [' RMF FM ', 'KATOWICE', '  NAJ-  ', ' LEPSZA ', ' MUZYKA '],
            ],
            [
                [   'x',        'xx',      'xxx',      'xxxx',    'xxxxx',    'xxxxxx',  'xxxxxxx',  'xxxxxxxx', 'xxxxxxxxx'],
                ['   x    ', '   xx   ', '  xxx   ', '  xxxx  ', ' xxxxx  ', ' xxxxxx ', 'xxxxxxx ', 'xxxxxxxx', 'xxxxxxxx' ],
            ],
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
