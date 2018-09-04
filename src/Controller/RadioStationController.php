<?php

namespace App\Controller;

use App\Entity\RadioStation;
use App\Entity\RadioTable;
use App\Form\RadioStationEditType;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class RadioStationController extends AbstractController
{
    /**
     * @Route("/dodaj-stacje/{radioTableId}", name="radiostation.add")
     * @ParamConverter("radioTable", options={"mapping": {"radioTableId": "id"}})
     */
    public function add(RadioTable $radioTable, Request $request, EntityManagerInterface $entityManager): Response
    {
        $radioStation = new RadioStation;

        $form = $this->createForm(RadioStationEditType::class, $radioStation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $radioStation->setRadioTable($radioTable);

            $entityManager->persist($radioStation);
            $entityManager->flush();
        }

        return $this->render('radiostation/add.html.twig', [
            'form'       => $form->createView(),
            'radiotable' => $radioTable,
        ]);
    }

    /**
     * @Route("/edytuj-stacje/{id}", name="radiostation.edit")
     */
    public function edit(RadioStation $radioStation, Request $request, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(RadioStationEditType::class, $radioStation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
        }

        return $this->render('radiostation/edit.html.twig', [
            'form'         => $form->createView(),
            'radiostation' => $radioStation,
        ]);
    }

    /**
     * @Route("/kopiuj-stacje/{radioTableId}", name="radiostation.copy")
     * @ParamConverter("radioTable", options={"mapping": {"radioTableId": "id"}})
     */
    public function copy(RadioTable $radioTable): Response
    {
        return $this->render('radiostation/copy.html.twig', [
            'radiotable' => $radioTable,
        ]);
    }

    /**
     * @Route("/usun-stacje/{id}", name="radiostation.remove")
     */
    public function remove(RadioStation $radioStation): Response
    {
        return $this->redirectToRoute('radiotable.remove', [
            'id' => $radioStation->getRadioTable()->getId(),
            'r'  => $radioStation->getId(),
        ]);
    }
}
