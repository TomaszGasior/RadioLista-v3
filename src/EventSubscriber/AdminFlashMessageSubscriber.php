<?php

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\FlashBagAwareSessionInterface;
use Symfony\Component\Translation\TranslatableMessage;

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

        if (!$session instanceof FlashBagAwareSessionInterface) {
            return;
        }

        $session->getFlashBag()->add(
            'error',
            new TranslatableMessage('common.notification.showing_private_content_as_administrator', [], 'admin')
        );
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
