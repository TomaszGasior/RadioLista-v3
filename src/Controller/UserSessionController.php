<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class UserSessionController extends AbstractController
{
    /**
     * @Route("/logowanie", name="user-session_login")
     */
    public function logIn()
    {
        return $this->render('user-session/login.html.twig');
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
