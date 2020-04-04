<?php

namespace App\Tests\Twig;

use App\Twig\AppExtension;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Twig\Environment;

class AppExtensionTest extends TestCase
{
    /**
     * @dataProvider csvProvider
     */
    public function testCsvEscaper(string $sourceString, string $escapedString): void
    {
        $extension = $this->getInstance();

        /** @var Environment|MockObject */
        $twigEnvironment = $this->createMock(Environment::class);

        $this->assertEquals(
            $escapedString,
            $extension->escapeCSV($twigEnvironment, $sourceString)
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
