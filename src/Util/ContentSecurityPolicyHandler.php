<?php

namespace App\Util;

/**
 * Handler for the Content-Security-Policy HTTP header value.
 */
class ContentSecurityPolicyHandler
{
    private const array DEFAULT_DIRECTIVES = [
        'default-src' => "'none'",
        'script-src' => "'self'",
        'style-src' => "'self'",
        'img-src' => "'self' data:",
        'font-src' => "'self'",
        'form-action' => "'self'",
        'connect-src' => "'self'",
    ];

    private array $directives = self::DEFAULT_DIRECTIVES;

    public function addDirective(string $value, string ...$directives): void
    {
        foreach ($directives as $name) {
            $this->directives[$name] = ($this->directives[$name] ?? '') . ' ' . $value;
        }
    }

    public function addNonce(string $directive): string
    {
        $nonce = bin2hex(random_bytes(16));

        $this->addDirective("'nonce-$nonce'", $directive);

        return $nonce;
    }

    public function getContentSecurityPolicy(): string
    {
        $directives = array_map(
            function (string $value) {
                if (str_contains($value, "'unsafe-inline'")) {
                    $value = preg_replace("/'nonce-[^' ]+'/", '', $value);
                }

                return $value;
            },
            $this->directives
        );

        return array_reduce(
            array_keys($directives),
            function (string $result, string $name) use ($directives) {
                return $result . $name . ' ' . $directives[$name] . '; ';
            },
            ''
        );
    }
}
