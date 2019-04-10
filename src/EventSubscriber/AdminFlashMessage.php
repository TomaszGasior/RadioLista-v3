<?php

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class AdminFlashMessage implements EventSubscriberInterface
{
    private $requestStack;

    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }

    public function onRestrictedAdminAccess(): void
    {
        $request = $this->requestStack->getCurrentRequest();

        $session = $request->getSession();
        $session->getFlashBag()->add('error', 'Przeglądasz prywatny zasób jako administrator.');
    }

    static public function getSubscribedEvents(): array
    {
        return [
            'app.restricted_admin_access' => 'onRestrictedAdminAccess',
        ];
    }
}
