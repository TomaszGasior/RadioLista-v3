<?php

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Event\FinishRequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Core\Authentication\Token\RememberMeToken;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Component\Security\Http\SecurityEvents;

class SecurityFlashNotice implements EventSubscriberInterface
{
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    public function onSecurityInteractiveLogin(InteractiveLoginEvent $event): void
    {
        if ($event->getAuthenticationToken() instanceof RememberMeToken) {
            return;
        }

        $session = $event->getRequest()->getSession();
        $session->getFlashBag()->add('notice', 'Zalogowano się pomyślnie.');
    }

    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();

        if (!($exception instanceof AccessDeniedException) || 404 === $exception->getCode()
            || null !== $this->security->getUser()) {
            return;
        }

        $session = $event->getRequest()->getSession();
        $session->getFlashBag()->add('error', 'Zaloguj się, aby mieć dostęp do tej strony.');
    }

    public function onKernelFinishRequest(FinishRequestEvent $event): void
    {
        $request = $event->getRequest();

        if ('security.logout' !== $request->attributes->get('_route')) {
            return;
        }

        $session = $request->getSession();
        $session->getFlashBag()->add('notice', 'Wylogowano się pomyślnie.');
    }

    static public function getSubscribedEvents(): array
    {
        return [
            SecurityEvents::INTERACTIVE_LOGIN => 'onSecurityInteractiveLogin',
            KernelEvents::EXCEPTION => ['onKernelException', 10],
            KernelEvents::FINISH_REQUEST => 'onKernelFinishRequest',
        ];
    }
}
