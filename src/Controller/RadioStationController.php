<?php

namespace App\Controller;

use App\Entity\RadioStation;
use App\Form\RadioStationEditType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
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
     * @Route("/edytuj-stacje/{id}", name="radiostation_edit")
     */
    public function edit(RadioStation $radioStation, Request $request, EntityManagerInterface $entityManager)
    {
        $form = $this->createForm(RadioStationEditType::class, $radioStation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
        }

        return $this->render('radiostation/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/kopiuj-stacje", name="radiostation_copy")
     */
    public function copy()
    {
        return $this->render('radiostation/copy.html.twig');
    }
}
