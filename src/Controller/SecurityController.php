<?php

namespace App\Controller;

use App\Form\SessionLogInType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    /**
     * @Route("/logowanie", name="security.login")
     */
    public function logIn(AuthenticationUtils $authenticationUtils)
    {
        $logInForm = $this->createForm(SessionLogInType::class, [
            'username' => $authenticationUtils->getLastUsername(),
        ], [
            'action' => $this->generateUrl('security.login'),
        ]);

        return $this->render('session/login.html.twig', [
            'error' => $authenticationUtils->getLastAuthenticationError(),
            'logInForm' => $logInForm->createView(),
        ]);
    }

    /**
     * @Route("/wyloguj", name="security.logout")
     */
    public function logOut()
    {
    }
}
