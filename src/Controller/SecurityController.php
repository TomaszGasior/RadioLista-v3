<?php

namespace App\Controller;

use App\Form\SecurityLoginType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Translation\TranslatorInterface;

class SecurityController extends AbstractController
{
    /**
     * @Route("/logowanie", name="security.login")
     * @Security("not is_granted('IS_AUTHENTICATED_REMEMBERED')", statusCode=400)
     */
    public function login(AuthenticationUtils $authenticationUtils,
                          TranslatorInterface $translator): Response
    {
        $form = $this->createForm(SecurityLoginType::class, [
            'username' => $authenticationUtils->getLastUsername(),
        ]);

        $error = $authenticationUtils->getLastAuthenticationError();
        if ($error) {
            $this->addFlash(
                'error',
                $translator->trans($error->getMessageKey(), [], 'security')
            );
        }

        return $this->render('security/login.html.twig', [
            'error' => $authenticationUtils->getLastAuthenticationError(),
            'form'  => $form->createView(),
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
