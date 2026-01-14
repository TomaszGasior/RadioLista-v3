<?php

namespace App\EventListener;

use App\Util\ContentSecurityPolicyHandler;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpKernel\Event\ResponseEvent;

class HttpSecurityListener
{
    public function __construct(private ContentSecurityPolicyHandler $cspHandler) {}

    #[AsEventListener]
    public function onKernelResponse(ResponseEvent $event): void
    {
        $response = $event->getResponse();

        $response->headers->set('Content-Security-Policy', $this->cspHandler->getContentSecurityPolicy());
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->headers->set('Strict-Transport-Security', 'max-age=2592000');
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');
        $response->headers->set('X-Content-Type-Options', 'nosniff');
    }
}
