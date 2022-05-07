<?php

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Session;

class AdminFlashMessageSubscriber implements EventSubscriberInterface
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

        // This triggers warning about untranslated string.
        $session->getFlashBag()->add('notice', 'Przeglądasz prywatny zasób jako administrator.');
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
