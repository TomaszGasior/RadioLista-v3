<?php

namespace App\EventListener;

use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\Security\Core\Exception\RuntimeException as SecurityException;
use Twig\Environment;

class MaintenanceModeListener
{
    public function __construct(
        #[Autowire('%kernel.project_dir%/var/lock/maintenance.lock')]
        private string $lockFilePath,

        private Security $security,
        private Environment $twig,
    ) {}

    #[AsEventListener(priority: -1)]
    public function onKernelRequest(RequestEvent $event): void
    {
        if (false === file_exists($this->lockFilePath)) {
            return;
        }

        if (
            $event->getRequest()->attributes->getBoolean('ignore_maintenance_mode', false)
            || !$event->isMainRequest()
        ) {
            return;
        }

        try {
            if ($this->security->isGranted('ROLE_ADMIN')) {
                return;
            }
        } catch (SecurityException $e) {}

        $response = new Response(
            $this->twig->render('dark-error.html.twig', ['message' => 'MaintenanceMode']),
            503
        );

        $event->setResponse($response);
    }
}
