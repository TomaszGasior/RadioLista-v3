<?php

namespace App\Controller;

use App\Entity\RadioTable;
use App\Form\RadioTableCreateType;
use App\Form\RadioTableSettingsType;
use App\Renderer\RadioTableRenderer;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class RadioTableController extends AbstractController
{
    /**
     * @Route("/wykaz/{id}", name="radiotable.show")
     */
    public function show(RadioTable $radioTable, RadioTableRenderer $radioTableRenderer): Response
    {
        $radioTableCode = $radioTableRenderer->render(
            $radioTable,
            null
        );

        return $this->render('radiotable/show.html.twig', [
            'radiotable'      => $radioTable,
            'radiotable_code' => $radioTableCode,
        ]);
    }

    /**
     * @Route("/utworz-wykaz", name="radiotable.create")
     */
    public function create(Request $request, EntityManagerInterface $entityManager): Response
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
     * @Route("/ustawienia-wykazu/{id}", name="radiotable.settings")
     */
    public function settings(RadioTable $radioTable, Request $request, EntityManagerInterface $entityManager): Response
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
     * @Route("/usun-wykaz", name="radiotable.remove")
     */
    public function remove(): Response
    {
        return $this->render('radiotable/remove.html.twig');
    }
}
