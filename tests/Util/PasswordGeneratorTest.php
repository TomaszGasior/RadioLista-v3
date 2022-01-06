<?php

namespace App\Tests\Util;

use App\Util\PasswordGenerator;
use PHPUnit\Framework\TestCase;

class PasswordGeneratorTest extends TestCase
{
    /** @var PasswordGenerator|MockObject */
    private $passwordGenerator;

    public function setUp(): void
    {
        $this->passwordGenerator = new PasswordGenerator;
    }

    public function testGetRandomPassword(): void
    {
        $firstPassword = $this->passwordGenerator->getRandomPassword();
        $secondPassword = $this->passwordGenerator->getRandomPassword();

        $this->assertNotEquals($secondPassword, $firstPassword);

        $this->assertEquals(20, mb_strlen($firstPassword));
        $this->assertEquals(20, mb_strlen($secondPassword));
    }
}
