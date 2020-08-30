<?php

namespace App\Tests\Twig;

use App\Twig\AppExtension;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Twig\Environment;

class AppExtensionTest extends TestCase
{
    public function csvProvider(): array
    {
        return [
            'no spaces' => [
                'something',
                'something',
            ],
            'spaces' => [
                'something with space',
                '"something with space"',
            ],
            'newlines' => [
                "something\nwith newline",
                "\"something\nwith newline\"",
            ],
        ];
    }

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

    private function getInstance(): AppExtension
    {
        $reflection = new \ReflectionClass(AppExtension::class);

        return $reflection->newInstanceWithoutConstructor();
    }
}
