<?php

namespace App\Security\Encoder;

use Symfony\Component\Security\Core\Encoder\MessageDigestPasswordEncoder;

class RLv1Encoder extends MessageDigestPasswordEncoder
{
    public function __construct()
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
