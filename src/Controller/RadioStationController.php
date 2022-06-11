<?php

namespace App\Controller;

use App\Entity\RadioStation;
use App\Entity\RadioTable;
use App\Form\RadioStationEditType;
use App\Form\RadioStationBulkRemoveType;
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
     * @Route(
     *     {"pl": "/wykaz/{radioTableId}/dodaj-stacje", "en": "/list/{radioTableId}/add-station"},
     *     name="radio_station.add"
     * )
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
     * @Route(
     *     {"pl": "/wykaz/{radioTableId}/edytuj-stacje/{id}", "en": "/list/{radioTableId}/edit-station/{id}"},
     *     name="radio_station.edit"
     * )
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
     * @Route(
     *     {"pl": "/wykaz/{radioTableId}/kopiuj-stacje/{id}", "en": "/list/{radioTableId}/copy-station/{id}"},
     *     name="radio_station.copy"
     * )
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
     * @Route(
     *     {"pl": "/wykaz/{radioTableId}/usun-stacje/{id}", "en": "/list/{radioTableId}/delete-station/{id}"},
     *     name="radio_station.remove",
     *     methods={"POST"}
     * )
     * @IsGranted("IS_AUTHENTICATED_REMEMBERED")
     * @IsGranted("RADIO_TABLE_MODIFY", subject="radioStation", statusCode=404)
     */
    public function remove(RadioStation $radioStation, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($radioStation);
        $entityManager->flush();

        $this->addFlash('notice', 'radio_station.remove.notification.removed');

        return $this->redirectToRoute('radio_table.show', [
            'id' => $radioStation->getRadioTable()->getId(),
        ]);
    }

    /**
     * @Route({"pl": "/wykaz/{id}/usun-stacje", "en": "/list/{id}/delete-stations"}, name="radio_station.bulk_remove")
     * @IsGranted("IS_AUTHENTICATED_REMEMBERED")
     * @IsGranted("RADIO_TABLE_MODIFY", subject="radioTable", statusCode=404)
     */
    public function bulkRemove(RadioTable $radioTable, Request $request, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(RadioStationBulkRemoveType::class, null, ['radio_table' => $radioTable]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $chosenToRemove = $form->getData()['chosenToRemove'];

            if (count($chosenToRemove) > 0) {
                foreach ($chosenToRemove as $radioStation) {
                    $entityManager->remove($radioStation);
                }
                $entityManager->flush();

                $this->addFlash('notice', 'radio_station.bulk_remove.notification.bulk_removed');

                // Form needs to be reloaded to not display removed radio stations.
                return $this->redirectToRoute('radio_station.bulk_remove', [
                    'id' => $radioTable->getId(),
                ]);
            }
        }

        return $this->render('radio_station/bulk_remove.html.twig', [
            'form' => $form->createView(),
            'radio_table' => $radioTable,
        ]);
    }
}
