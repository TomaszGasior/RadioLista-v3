<?php

namespace App\Controller;

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
     * @Route("/wykaz/{radioTableId}/dodaj-stacje", name="radio_station.add")
     * @ParamConverter("radioTable", options={"mapping": {"radioTableId": "id"}})
     * @ParamConverter("template", class="stdClass")
     * @IsGranted("IS_AUTHENTICATED_REMEMBERED")
     * @IsGranted("RADIO_TABLE_MODIFY", subject="radioTable", statusCode=404)
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

            $this->addFlash('notice', 'radio_station.add.notification.added');
            return $this->redirectToRoute('radio_station.edit', [
                'id' => $radioStation->getId(),
                'radioTableId' => $radioTable->getId(),
            ]);
        }

        return $this->render('radio_station/add.html.twig', [
            'form' => $form->createView(),
            'radio_station' => $radioStation,
        ]);
    }

    /**
     * @Route("/wykaz/{radioTableId}/edytuj-stacje/{id}", name="radio_station.edit")
     * @IsGranted("IS_AUTHENTICATED_REMEMBERED")
     * @IsGranted("RADIO_TABLE_MODIFY", subject="radioStation", statusCode=404)
     */
    public function edit(RadioStation $radioStation, Request $request,
                         EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(RadioStationEditType::class, $radioStation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('notice', 'common.notification.saved_changes');
        }

        return $this->render('radio_station/edit.html.twig', [
            'form' => $form->createView(),
            'radio_station' => $radioStation,
        ]);
    }

    /**
     * @Route("/wykaz/{radioTableId}/kopiuj-stacje/{id}", name="radio_station.copy")
     * @IsGranted("IS_AUTHENTICATED_REMEMBERED")
     * @IsGranted("RADIO_TABLE_MODIFY", subject="radioStation", statusCode=404)
     */
    public function copy(RadioStation $radioStation): Response
    {
        $template = clone $radioStation;

        $this->addFlash('notice', 'radio_station.add.notification.copied');

        return $this->forward(__CLASS__ . '::add', [
            'radioTableId' => $radioStation->getRadioTable()->getId(),
            'template' => $template,
        ]);
    }

    /**
     * @Route("/wykaz/{radioTableId}/usun-stacje/{id}", name="radio_station.remove")
     * @IsGranted("IS_AUTHENTICATED_REMEMBERED")
     * @IsGranted("RADIO_TABLE_MODIFY", subject="radioStation", statusCode=404)
     */
    public function remove(RadioStation $radioStation): Response
    {
        $this->addFlash('notice', 'radio_station.remove.notification.chosen_to_be_removed');

        return $this->forward(RadioTableController::class . '::remove', [
            'id' => $radioStation->getRadioTable()->getId(),
            'radioStationToRemove' => $radioStation,
        ]);
    }
}
