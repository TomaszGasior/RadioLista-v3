<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class UserPagesController extends AbstractController
{
    /**
     * @Route("/profil", name="user_profile")
     */
    public function publicProfile()
    {
        return $this->render('user-pages/public-profile.html.twig');
    }

    /**
     * @Route("/moje-wykazy", name="user_radiotables")
     */
    public function radioTablesList()
    {
        return $this->render('user-pages/radiotables-list.html.twig');
    }

    /**
     * @Route("/ustawienia-konta", name="user_settings")
     */
    public function accountSettings()
    {
        return $this->render('user-pages/account-settings.html.twig');
    }
}
