<?php

namespace App\Controller;

use App\Entity\User;
use App\Renderer\RadioTablesListRenderer;
use App\Repository\RadioTableRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    /**
     * @Route("/profil/{name}", name="user.public_profile")
     */
    public function publicProfile(User $user, RadioTablesListRenderer $radioTablesListRenderer)
    {
        if (!$user->getPublicProfile()) {
            throw $this->createNotFoundException();
        }

        $radioTablesList = $radioTablesListRenderer->render(
            $user->getRadioTables(),
            null
        );

        return $this->render('user/public-profile.html.twig', [
            'user' => $user,
            'radioTablesList' => $radioTablesList,
        ]);
    }

    /**
     * @Route("/moje-wykazy", name="user.my_radiotables")
     */
    public function myRadioTables(RadioTableRepository $radioTableRepository,
                                  RadioTablesListRenderer $radioTablesListRenderer)
    {
        $radioTables = $radioTableRepository->findOwnedByUser($this->getUser());
        $radioTablesList = $radioTablesListRenderer->render(
            $radioTables,
            RadioTablesListRenderer::OPTION_SHOW_VISIBILITY | RadioTablesListRenderer::OPTION_SHOW_ACTIONS
        );

        return $this->render('user/my-radiotables.html.twig', [
            'radioTablesList' => $radioTablesList,
        ]);
    }

    /**
     * @Route("/ustawienia-konta", name="user.my_account_settings")
     */
    public function myAccountSettings()
    {
        return $this->render('user/account-settings.html.twig');
    }

    /**
     * @Route("/rejestracja", name="user.register")
     */
    public function register()
    {
        return $this->render('session/register.html.twig');
    }
}
