<?php

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ResponseEvent;

class HttpSecuritySubscriber implements EventSubscriberInterface
{
    private $cspExtraDomains;

    public function __construct(array $cspExtraDomains = [])
    {
        $this->cspExtraDomains = $cspExtraDomains;
    }

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

    public static function getSubscribedEvents(): array
    {
        return [
            ResponseEvent::class => 'onKernelResponse',
        ];
    }
}
