<?php

namespace App\Tests\Securty\Hasher;

use App\Entity\User;
use App\Security\Hasher\RLv1PasswordHasher;
use App\Util\ReflectionUtilsTrait;
use DateTime;
use PHPUnit\Framework\TestCase;
use Symfony\Component\PasswordHasher\Hasher\PasswordHasherFactoryInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasher;
use Symfony\Component\PasswordHasher\PasswordHasherInterface;

class RLv1PasswordHasherTest extends TestCase
{
    use ReflectionUtilsTrait;

    private const USER_REGISTER_DATE = '2022-06-01';
    private const PASSWORD_HASH = '01a111fd79fac0aa8130a27496c57b96143a8586';

    private const CORRECT_PASSWORD = 'radiolista';
    private const INCORRECT_PASSWORD = 'something different';

    public function test_hasher_correctly_verifies_legacy_password(): void
    {
        $this->assertTrue($this->isPasswordValid(self::CORRECT_PASSWORD));
    }

    public function test_hasher_rejects_invalid_legacy_password(): void
    {
        $this->assertFalse($this->isPasswordValid(self::INCORRECT_PASSWORD));
    }

    private function isPasswordValid(string $password): bool
    {
        $userPasswordHasher = new UserPasswordHasher(
            new class implements PasswordHasherFactoryInterface
            {
                public function getPasswordHasher($user): PasswordHasherInterface
                {
                    return new RLv1PasswordHasher;
                }
            }
        );

        $user = new User;
        $user->setPasswordHash(self::PASSWORD_HASH);

        $this->setPrivateFieldOfObject($user, 'registerDate', new DateTime(self::USER_REGISTER_DATE));

        return $userPasswordHasher->isPasswordValid($user, $password);
    }
}
