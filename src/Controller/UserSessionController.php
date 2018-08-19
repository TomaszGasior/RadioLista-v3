<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class UserSessionController extends AbstractController
{
    /**
     * @Route("/logowanie", name="user-session_login")
     */
    public function logIn(AuthenticationUtils $authenticationUtils)
    {
        return $this->render('user-session/login.html.twig', [
            'error'         => $authenticationUtils->getLastAuthenticationError(),
            'last_username' => $authenticationUtils->getLastUsername(),
        ]);
    }

    /**
     * @Route("/rejestracja", name="user-session_register")
     */
    public function register()
    {
        return $this->render('user-session/register.html.twig');
    }

    /**
     * @Route("/wyloguj", name="user-session_logout")
     */
    public function logOut()
    {
    }
}
