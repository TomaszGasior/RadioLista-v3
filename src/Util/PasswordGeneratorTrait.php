<?php

namespace App\Util;

trait PasswordGeneratorTrait
{
    /**
     * https://paragonie.com/blog/2015/07/how-safely-generate-random-strings-and-integers-in-php
     */
    protected function getRandomPassword(int $passwordLength = 20): string
    {
        if ($passwordLength < 1) {
            throw new \InvalidArgumentException;
        }

        $alphabet = '!"#$%&\'()*+,-./0123456789:;<=>?@ABCDEFGHIJKLMNOPQRSTUVWXYZ[\]^_`abcdefghijklmnopqrstuvwxyz{|}~';
        $alphabetLength = strlen($alphabet) - 1;

        $password = '';

        for ($i = 0; $i < $passwordLength; $i++) {
            $password .= $alphabet[random_int(0, $alphabetLength)];
        }

        return $password;
    }
}
