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

        return $this->render('user/public_profile.html.twig', [
            'user' => $user,
            'radiotables_list' => $radioTablesList,
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

        return $this->render('user/my_radiotables.html.twig', [
            'radiotables_list' => $radioTablesList,
        ]);
    }

    /**
     * @Route("/ustawienia-konta", name="user.my_account_settings")
     */
    public function myAccountSettings()
    {
        return $this->render('user/my_account_settings.html.twig');
    }

    /**
     * @Route("/rejestracja", name="user.register")
     */
    public function register()
    {
        return $this->render('user/register.html.twig');
    }
}
