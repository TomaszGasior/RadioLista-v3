<?php

namespace App\Security;

use Symfony\Component\Security\Core\Encoder\MessageDigestPasswordEncoder;

// Encoder used for backward compatibility with passwords hashed in RLv1.

class RLv1Encoder extends MessageDigestPasswordEncoder
{
    public function __construct(string $algorithm = 'sha512', bool $encodeHashAsBase64 = true, int $iterations = 5000)
    {
        parent::__construct('sha1', false, 1);
    }

    protected function mergePasswordAndSalt($password, $salt)
    {
        return $salt . $password;
    }

    protected function demergePasswordAndSalt($mergedPasswordSalt)
    {
        throw new \RuntimeException;
    }
}