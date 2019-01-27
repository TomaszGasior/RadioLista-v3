<?php

namespace App\Controller;

use App\Controller\RadioTableController;
use App\Entity\RadioStation;
use App\Entity\RadioTable;
use App\Form\RadioStationEditType;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
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
     * @ParamConverter("template", class="stdClass")
     * @IsGranted("RADIOTABLE_MODIFY", subject="radioTable")
     */
    public function add(RadioTable $radioTable, Request $request, EntityManagerInterface $entityManager,
                        RadioStation $template = null): Response
    {
        $radioStation = $template ? $template : new RadioStation;
        $radioStation->setRadioTable($radioTable);

        $form = $this->createForm(RadioStationEditType::class, $radioStation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($radioStation);
            $entityManager->flush();

            $this->addFlash('notice', 'Stacja została dodana.');
            return $this->redirectToRoute('radiostation.edit', ['id' => $radioStation->getId()]);
        }

        return $this->render('radiostation/add.html.twig', [
            'form'         => $form->createView(),
            'radiostation' => $radioStation,
        ]);
    }

    /**
     * @Route("/edytuj-stacje/{id}", name="radiostation.edit")
     * @IsGranted("RADIOTABLE_MODIFY", subject="radioStation")
     */
    public function edit(RadioStation $radioStation, Request $request,
                         EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(RadioStationEditType::class, $radioStation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('notice', 'Stacja została zapisana.');
        }

        return $this->render('radiostation/edit.html.twig', [
            'form'         => $form->createView(),
            'radiostation' => $radioStation,
        ]);
    }

    /**
     * @Route("/kopiuj-stacje/{id}", name="radiostation.copy")
     * @IsGranted("RADIOTABLE_MODIFY", subject="radioStation")
     */
    public function copy(RadioStation $radioStation): Response
    {
        $template = clone $radioStation;

        $this->addFlash('notice', 'Dane stacji zostały wypełnione.');

        return $this->forward(__CLASS__ . '::add', [
            'radioTableId' => $radioStation->getRadioTable()->getId(),
            'template'     => $template,
        ]);
    }

    /**
     * @Route("/usun-stacje/{id}", name="radiostation.remove")
     * @IsGranted("RADIOTABLE_MODIFY", subject="radioStation")
     */
    public function remove(RadioStation $radioStation): Response
    {
        $this->addFlash('notice', 'Stacja została zaznaczona do usunięcia.');

        return $this->forward(RadioTableController::class . '::remove', [
            'id'                   => $radioStation->getRadioTable()->getId(),
            'radioStationToRemove' => $radioStation,
        ]);
    }
}
