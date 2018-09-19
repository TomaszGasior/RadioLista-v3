<?php

namespace App\EventListener;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Session;

class SecurityFlashNotice
{
    private $requestStack;

    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }

    public function onSecurityInteractiveLogin()
    {
        $session = $this->requestStack->getCurrentRequest()->getSession();

        $session->getFlashBag()->add('notice', 'Zalogowano się pomyślnie.');
    }
}
