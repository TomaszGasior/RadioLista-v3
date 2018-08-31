<?php

namespace App\Controller;

use App\Entity\RadioTable;
use App\Form\RadioTableCreateType;
use App\Form\RadioTableSettingsType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class RadioTableController extends AbstractController
{
    /**
     * @Route("/wykaz/{id}", name="radiotable_show")
     */
    public function show(RadioTable $radioTable)
    {
        return $this->render('radiotable/show.html.twig');
    }

    /**
     * @Route("/utworz-wykaz", name="radiotable_create")
     */
    public function create(Request $request, EntityManagerInterface $entityManager)
    {
        $radioTable = new RadioTable;

        $form = $this->createForm(RadioTableCreateType::class, $radioTable);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $radioTable->setOwner($this->getUser());

            $entityManager->persist($radioTable);
            $entityManager->flush();
        }

        return $this->render('radiotable/create.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/ustawienia-wykazu/{id}", name="radiotable_settings")
     */
    public function settings(RadioTable $radioTable, Request $request, EntityManagerInterface $entityManager)
    {
        $form = $this->createForm(RadioTableSettingsType::class, $radioTable);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
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
