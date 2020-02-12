<?php

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Core\Exception\RuntimeException as SecurityException;
use Symfony\Component\Security\Core\Security;
use Twig\Environment;

class MaintenanceMode implements EventSubscriberInterface
{
    private $lockFilePath;
    private $security;
    private $twig;

    public function __construct(string $lockFilePath, Security $security, Environment $twig)
    {
        $this->lockFilePath = $lockFilePath;
        $this->security = $security;
        $this->twig = $twig;
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        if (false === file_exists($this->lockFilePath)) {
            return;
        }

        try {
            if ($this->security->isGranted('ROLE_ADMIN')) {
                return;
            }
        } catch (SecurityException $e) {}

        // Don't enable maintenance mode on development environment.
        // It breaks Symfony debug toolbar and profiler.
        $response = new Response(
            $this->twig->render('dark-error.html.twig', ['message' => 'MaintenanceMode']),
            503
        );
        $event->setResponse($response);
    }

    /**
     * @codeCoverageIgnore
     */
    static public function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => 'onKernelRequest',
        ];
    }
}
