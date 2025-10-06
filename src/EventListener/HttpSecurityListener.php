<?php

namespace App\EventListener;

use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpKernel\Event\ResponseEvent;

class HttpSecurityListener
{
    /**
     * @param string[] $cspExtraDomains
     */
    public function __construct(
        #[Autowire('@=[ env("default::key:url:json:TRACKER_SETTINGS") ]')]
        private array $cspExtraDomains,
    ) {}

    #[AsEventListener]
    public function onKernelResponse(ResponseEvent $event): void
    {
        $response = $event->getResponse();

        $cspSources = implode(' ', array_merge(
            ["'self'", "'unsafe-inline'", 'data:'],
            $this->cspExtraDomains
        ));

        $response->headers->set('Content-Security-Policy', 'default-src ' . $cspSources);
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->headers->set('Strict-Transport-Security', 'max-age=2592000');
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');
        $response->headers->set('X-Content-Type-Options', 'nosniff');
    }
}
