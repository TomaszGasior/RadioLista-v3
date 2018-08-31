<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\RadioTableRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    /**
     * @Route("/profil/{name}", name="user_profile")
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
     * @Route("/moje-wykazy", name="user_radiotables")
     */
    public function myRadioTables(RadioTableRepository $radioTableRepository)
    {
        $radioTablesList = $radioTableRepository->findOwnedByUser($this->getUser());

        return $this->render('user/my-radiotables.html.twig', [
            'radioTablesList' => $radioTablesList,
        ]);
    }

    /**
     * @Route("/ustawienia-konta", name="user_settings")
     */
    public function accountSettings()
    {
        return $this->render('user/account-settings.html.twig');
    }
}
