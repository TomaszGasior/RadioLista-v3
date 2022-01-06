<?php

namespace App\Util;

/**
 * https://paragonie.com/blog/2015/07/how-safely-generate-random-strings-and-integers-in-php
 */
class PasswordGenerator
{
    private const ALPHABET = '!"#$%&\'()*+,-./0123456789:;<=>?@ABCDEFGHIJKLMNOPQRSTUVWXYZ[\]^_`abcdefghijklmnopqrstuvwxyz{|}~';
    private const PASSWORD_LENGTH = 20;

    public function getRandomPassword(): string
    {
        $alphabetLength = strlen(self::ALPHABET) - 1;

        $password = '';

        for ($i = 0; $i < self::PASSWORD_LENGTH; $i++) {
            $password .= self::ALPHABET[random_int(0, $alphabetLength)];
        }

        return $password;
    }
}
