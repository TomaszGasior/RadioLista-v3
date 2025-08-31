<?php

namespace App\Security\Hasher;

use Symfony\Component\PasswordHasher\Exception\InvalidPasswordException;
use Symfony\Component\PasswordHasher\Hasher\CheckPasswordLengthTrait;
use Symfony\Component\PasswordHasher\LegacyPasswordHasherInterface;

class RLv1PasswordHasher implements LegacyPasswordHasherInterface
{
    use CheckPasswordLengthTrait;

    public function hash(string $plainPassword, ?string $salt = null): string
    {
        if ($this->isPasswordTooLong($plainPassword)) {
            throw new InvalidPasswordException;
        }

        return hash('sha1', $salt.$plainPassword);
    }

    public function verify(string $hashedPassword, string $plainPassword, ?string $salt = null): bool
    {
        return !$this->isPasswordTooLong($plainPassword) &&
               hash_equals($hashedPassword, $this->hash($plainPassword, $salt));
    }

    public function needsRehash(string $hashedPassword): bool
    {
        return false;
    }
}
