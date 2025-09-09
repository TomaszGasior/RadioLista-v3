<?php

namespace App\Tests\Twig;

use App\Twig\RadioTableExtension;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Twig\Environment;

class RadioTableExtensionTest extends TestCase
{
    static public function rdsFramesProvider(): iterable
    {
        $frames = [
            'POLSKIE' => 'POLSKIE ',
            ' RADIO'  => '  RADIO ',
            'JEDYNKA' => 'JEDYNKA ',
            ' 20:00'  => '  20:00 ',

            'x'         => '   x    ',
            'xx'        => '   xx   ',
            'xxx'       => '  xxx   ',
            'xxxx'      => '  xxxx  ',
            'xxxxx'     => ' xxxxx  ',
            'xxxxxx'    => ' xxxxxx ',
            'xxxxxxx'   => 'xxxxxxx ',
            'xxxxxxxx'  => 'xxxxxxxx',
            'xxxxxxxxx' => 'xxxxxxxx',
        ];

        foreach ($frames as $frame => $expectedFrame) {
            yield $frame => [$frame, $expectedFrame];
        }
    }

    #[DataProvider('rdsFramesProvider')]
    public function test_frame_is_properly_aligned_to_center(string $frame, string $expectedFrame): void
    {
        $extension = new RadioTableExtension;

        $this->assertEquals($expectedFrame, $extension->alignRDSFrame($frame));
    }
}
