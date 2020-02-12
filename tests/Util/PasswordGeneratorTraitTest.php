<?php

namespace App\Tests\Util;

use App\Util\PasswordGeneratorTrait;
use PHPUnit\Framework\TestCase;

class PasswordGeneratorTraitTest extends TestCase
{
    /** @var PasswordGeneratorTrait|MockObject */
    private $passwordGenerator;

    public function setUp(): void
    {
        $this->passwordGenerator = new class {
            use PasswordGeneratorTrait { getRandomPassword as public; }
        };
    }

    public function testGetRandomPassword(): void
    {
        $firstPassword = $this->passwordGenerator->getRandomPassword(33);
        $secondPassword = $this->passwordGenerator->getRandomPassword(43);

        $this->assertTrue(33 === mb_strlen($firstPassword));
        $this->assertTrue(43 === mb_strlen($secondPassword));
        $this->assertNotEquals($secondPassword, $firstPassword);
    }

    public function testThrowExceptionOnZeroLength(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $this->passwordGenerator->getRandomPassword(0);
    }

    public function testThrowExceptionOnNegativeLength(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $this->passwordGenerator->getRandomPassword(-10);
    }
}
