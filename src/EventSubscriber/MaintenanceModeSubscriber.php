<?php

namespace App\EventSubscriber;

use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\Security\Core\Exception\RuntimeException as SecurityException;
use Twig\Environment;

/**
 * Don't enable maintenance mode on development environment.
 * It breaks Symfony debug toolbar and profiler.
 */
class MaintenanceModeSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private string $lockFilePath,
        private Security $security,
        private Environment $twig,
    ) {}

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
            RequestEvent::class => 'onKernelRequest',
        ];
    }
}
