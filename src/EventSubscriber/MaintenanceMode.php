<?php

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Core\Security;
use Twig\Environment;

class MaintenanceMode implements EventSubscriberInterface
{
    private $maintenanceMode;
    private $security;
    private $twig;

    public function __construct(bool $maintenanceMode, Security $security, Environment $twig)
    {
        $this->maintenanceMode = $maintenanceMode;
        $this->security = $security;
        $this->twig = $twig;
    }

    public function onKernelRequest(GetResponseEvent $event): void
    {
        if (!$this->maintenanceMode) {
            return;
        }

        // This line breaks Symfony profiler. Don't run maintenance mode on dev env. ;-)
        if ($this->security->isGranted('ROLE_ADMIN')) {
            return;
        }

        $response = new Response(
            $this->twig->render('dark-error.html.twig', ['message' => 'MaintenanceMode'])
        );
        $event->setResponse($response);
    }

    static public function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => 'onKernelRequest',
        ];
    }
}
