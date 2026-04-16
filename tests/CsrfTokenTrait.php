<?php

namespace App\Tests;

use RuntimeException;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

trait CsrfTokenTrait
{
    private function stubCsrfTokenManager(): void
    {
        if (!$this instanceof KernelTestCase) {
            throw new RuntimeException;
        }

        self::getContainer()->set(
            CsrfTokenManagerInterface::class,
            new class implements CsrfTokenManagerInterface
            {
                public function getToken(string $tokenId): CsrfToken
                {
                    return new CsrfToken($tokenId, 'token');
                }

                public function refreshToken(string $tokenId): CsrfToken
                {
                    return new CsrfToken($tokenId, 'token');
                }

                public function removeToken(string $tokenId): ?string
                {
                    return 'token';
                }

                public function isTokenValid(CsrfToken $token): bool
                {
                    return $token->getValue() === 'token';
                }
            }
        );
    }
}
