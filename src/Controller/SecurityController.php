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
    /**
     * @Route("/logowanie", name="security.login")
     */
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
            $token = $error->getToken();

            if (!$token || !$token->getUsername() || !$token->getCredentials()) {
                $this->addFlash('error', 'Nie podano nazwy użytkownika lub hasła.');
            }
            elseif ($error instanceof BadCredentialsException) {
                $this->addFlash('error', 'Dane logowania są niepoprawne.');
            }
            else {
                $this->addFlash('error', 'Wystąpił błąd. Spróbuj ponownie później.');
            }
        }

        return $this->render('security/login.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/wyloguj", name="security.logout")
     */
    public function logout(): Response
    {
        throw new \Exception;
    }
}
