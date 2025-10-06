<?php

namespace App\EventListener;

use App\Event\AdminRestrictedAccessEvent;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\FlashBagAwareSessionInterface;
use Symfony\Component\Translation\TranslatableMessage;

class AdminFlashMessageListener
{
    public function __construct(private RequestStack $requestStack) {}

    #[AsEventListener(AdminRestrictedAccessEvent::class)]
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
}
