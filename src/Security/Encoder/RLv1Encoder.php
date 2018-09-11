<?php

namespace App\Security\Encoder;

use Symfony\Component\Security\Core\Encoder\MessageDigestPasswordEncoder;

class RLv1Encoder extends MessageDigestPasswordEncoder
{
    public function __construct(string $algorithm = 'sha512', bool $encodeHashAsBase64 = true,
                                int $iterations = 5000)
    {
        parent::__construct('sha1', false, 1);
    }

    protected function mergePasswordAndSalt($password, $salt): string
    {
        return $salt . $password;
    }

    protected function demergePasswordAndSalt($mergedPasswordSalt): string
    {
        throw new \RuntimeException;
    }
}
