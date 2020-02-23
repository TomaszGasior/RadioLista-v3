<?php

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Session;

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

        /** @var Session */
        $session = $request->getSession();

        // TODO: get rid of this ugly workaround!
        $session->getFlashBag()->add('validation_error', 'Przeglądasz prywatny zasób jako administrator.');
    }

    /**
     * @codeCoverageIgnore
     */
    static public function getSubscribedEvents(): array
    {
        return [
            'app.restricted_admin_access' => 'onRestrictedAdminAccess',
        ];
    }
}
