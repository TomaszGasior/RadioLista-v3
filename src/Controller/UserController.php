<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\RadioTableRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    /**
     * @Route("/profil/{name}", name="user.public_profile")
     */
    public function publicProfile(User $user)
    {
        if (!$user->getPublicProfile()) {
            throw $this->createNotFoundException();
        }

        return $this->render('user/public-profile.html.twig', [
            'user' => $user,
        ]);
    }

    /**
     * @Route("/moje-wykazy", name="user.my_radiotables")
     */
    public function myRadioTables(RadioTableRepository $radioTableRepository)
    {
        $radioTablesList = $radioTableRepository->findOwnedByUser($this->getUser());

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
