<?php

namespace App\EventListener;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Security\Core\Security;

class SecurityFlashNotice
{
    private $requestStack;
    private $security;

    public function __construct(RequestStack $requestStack, Security $security)
    {
        $this->requestStack = $requestStack;
        $this->security = $security;
    }

    public function onSecurityInteractiveLogin()
    {
        // Do not show login message when user is logged in by "remember me" feature.
        if (!$this->security->isGranted('IS_AUTHENTICATED_FULLY')) {
            return false;
        }

        $session = $this->requestStack->getCurrentRequest()->getSession();

        $session->getFlashBag()->add('notice', 'Zalogowano się pomyślnie.');
    }
}
