<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class RadioStationController extends AbstractController
{
    /**
     * @Route("/dodaj-stacje", name="radiostation_add")
     */
    public function add()
    {
        return $this->render('radiostation/add.html.twig');
    }

    /**
     * @Route("/edytuj-stacje", name="radiostation_edit")
     */
    public function edit()
    {
        return $this->render('radiostation/edit.html.twig');
    }

    /**
     * @Route("/kopiuj-stacje", name="radiostation_copy")
     */
    public function copy()
    {
        return $this->render('radiostation/copy.html.twig');
    }
}
