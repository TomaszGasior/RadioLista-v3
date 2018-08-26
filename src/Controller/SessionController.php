<?php

namespace App\Controller;

use App\Form\SessionLogInType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SessionController extends AbstractController
{
    /**
     * @Route("/logowanie", name="session_login")
     */
    public function logIn(AuthenticationUtils $authenticationUtils)
    {
        $logInForm = $this->createForm(SessionLogInType::class, [
            'username' => $authenticationUtils->getLastUsername(),
        ], [
            'action' => $this->generateUrl('session_login'),
        ]);

        return $this->render('session/login.html.twig', [
            'error' => $authenticationUtils->getLastAuthenticationError(),
            'logInForm' => $logInForm->createView(),
        ]);
    }

    /**
     * @Route("/rejestracja", name="session_register")
     */
    public function register()
    {
        return $this->render('session/register.html.twig');
    }

    /**
     * @Route("/wyloguj", name="session_logout")
     */
    public function logOut()
    {
    }
}
