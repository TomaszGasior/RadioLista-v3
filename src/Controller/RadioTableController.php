<?php

namespace App\Controller;

use App\Entity\RadioTable;
use App\Form\RadioTableCreateType;
use App\Form\RadioTableSettingsType;
use App\Repository\RadioStationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Routing\Annotation\Route;

class RadioTableController extends AbstractController
{
    /**
     * @Route("/wykaz/{id}", name="radiotable.show")
     * @IsGranted("RADIOTABLE_SHOW", subject="radioTable", statusCode=404)
     */
    public function show(RadioTable $radioTable, RadioStationRepository $radioStationRepository): Response
    {
        $radioStations = $radioStationRepository->findForRadioTable($radioTable);

        return $this->render('radiotable/show.html.twig', [
            'radiotable'    => $radioTable,
            'radiostations' => $radioStations,
        ]);
    }

    /**
     * @Route("/utworz-wykaz", name="radiotable.create")
     * @IsGranted("IS_AUTHENTICATED_REMEMBERED")
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

            $this->addFlash('notice', 'Wykaz został utworzony.');
            return $this->redirectToRoute('user.my_radiotables');
        }

        return $this->render('radiotable/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/ustawienia-wykazu/{id}", name="radiotable.settings")
     * @IsGranted("RADIOTABLE_MODIFY", subject="radioTable")
     */
    public function settings(RadioTable $radioTable, Request $request,
                             EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(RadioTableSettingsType::class, $radioTable);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('notice', 'Zmiany zostały zapisane.');
        }

        return $this->render('radiotable/settings.html.twig', [
            'form'       => $form->createView(),
            'radiotable' => $radioTable,
        ]);
    }

    /**
     * @Route("/eksport-wykazu/{id}/{_format}", name="radiotable.download", requirements={"_format": "csv|html|pdf"})
     * @IsGranted("RADIOTABLE_MODIFY", subject="radioTable")
     */
    public function download(RadioTable $radioTable, string $_format,
                             RadioStationRepository $radioStationRepository): Response
    {
        $radioStations = $radioStationRepository->findForRadioTable($radioTable);

        switch ($_format) {
            case 'html':
                $response = $this->render('radiotable/standalone.html.twig', [
                    'radiotable'    => $radioTable,
                    'radiostations' => $radioStations,
                ]);
                $response->headers->set('Content-Type', 'text/html');
                break;
            case 'csv':
                $response = $this->render('radiotable/table/radiotable.csv.twig', [
                    'radiotable'    => $radioTable,
                    'radiostations' => $radioStations,
                ]);
                $response->headers->set('Content-Type', 'text/csv');
                break;
            case 'pdf':
                throw new \Exception;
                $response->headers->set('Content-Type', 'application/pdf');
                break;
        }

        $response->headers->set(
            'Content-Disposition',
            $response->headers->makeDisposition(
                ResponseHeaderBag::DISPOSITION_ATTACHMENT,
                date('Y-m-d_H-i-s') . '.' . $_format
            )
        );

        return $response;
    }

    /**
     * @Route("/eksport-wykazu/{id}", name="radiotable.export")
     * @IsGranted("RADIOTABLE_MODIFY", subject="radioTable")
     */
    public function export(RadioTable $radioTable): Response
    {
        return $this->redirectToRoute('radiotable.settings', [
            'id'  => $radioTable->getId(),
            'tab' => 4,
        ]);
    }

    /**
     * @Route("/usun-wykaz/{id}", name="radiotable.remove")
     * @IsGranted("RADIOTABLE_MODIFY", subject="radioTable")
     */
    public function remove(RadioTable $radioTable): Response
    {
        return $this->render('radiotable/remove.html.twig', [
            'radiotable' => $radioTable,
        ]);
    }
}
