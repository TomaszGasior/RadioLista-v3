<?php

namespace App\Controller;

use App\Form\SecurityLoginType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    #[Route(['pl' => '/logowanie', 'en' => '/login'], name: 'security.login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        if (null !== $this->getUser()) {
            return $this->redirectToRoute('homepage');
        }

        $form = $this->createForm(SecurityLoginType::class, [
            'username' => $authenticationUtils->getLastUsername(),
        ]);

        $error = $authenticationUtils->getLastAuthenticationError();
        if ($error) {
            if ($error instanceof BadCredentialsException) {
                $this->addFlash('error', 'session.login.notification.bad_credentials');
            }
            else {
                $this->addFlash('error', 'session.login.notification.auth_error');
            }
        }

        return $this->render('security/login.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route(['pl' => '/wyloguj', 'en' => '/logout'], name: 'security.logout')]
    public function logout(): Response
    {
        throw new \Exception;
    }
}
