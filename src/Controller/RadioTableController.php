<?php

namespace App\Controller;

use App\Entity\RadioTable;
use App\Form\RadioTableSettingsType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
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
     * @Route("/ustawienia-wykazu/{id}", name="radiotable_settings")
     */
    public function settings(RadioTable $radioTable, Request $request)
    {
        $form = $this->createForm(RadioTableSettingsType::class, $radioTable);

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            dump($form->getData());exit;
        }

        return $this->render('radiotable/settings.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/usun-wykaz", name="radiotable_remove")
     */
    public function remove()
    {
        return $this->render('radiotable/remove.html.twig');
    }
}
