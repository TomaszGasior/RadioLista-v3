<?php

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\FlashBagAwareSessionInterface;

class AdminFlashMessageSubscriber implements EventSubscriberInterface
{
    public function __construct(private RequestStack $requestStack) {}

    public function onRestrictedAdminAccess(): void
    {
        $request = $this->requestStack->getCurrentRequest();

        if (!$request) {
            return;
        }

        $session = $request->getSession();

        if ($session instanceof FlashBagAwareSessionInterface) {
            // This triggers warning about untranslated string.
            $session->getFlashBag()->add('notice', 'Przeglądasz prywatny zasób jako administrator.');
        }
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
