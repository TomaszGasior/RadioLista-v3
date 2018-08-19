<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    /**
     * @Route("/profil", name="user_profile")
     */
    public function publicProfile()
    {
        return $this->render('user/public-profile.html.twig');
    }

    /**
     * @Route("/moje-wykazy", name="user_radiotables")
     */
    public function myRadioTables()
    {
        return $this->render('user/my-radiotables.html.twig');
    }

    /**
     * @Route("/ustawienia-konta", name="user_settings")
     */
    public function accountSettings()
    {
        return $this->render('user/account-settings.html.twig');
    }
}
