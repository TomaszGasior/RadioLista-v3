<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class RadioTableController extends AbstractController
{
    /**
     * @Route("/wykaz", name="radiotable_show")
     */
    public function show()
    {
        return $this->render('radiotable/show.html.twig');
    }

    /**
     * @Route("/utworz-wykaz", name="radiotable_create")
     */
    public function create()
    {
        return $this->render('radiotable/create.html.twig');
    }

    /**
     * @Route("/ustawienia-wykazu", name="radiotable_settings")
     */
    public function settings()
    {
        return $this->render('radiotable/settings.html.twig');
    }

    /**
     * @Route("/usun-wykaz", name="radiotable_remove")
     */
    public function remove()
    {
        return $this->render('radiotable/remove.html.twig');
    }
}
