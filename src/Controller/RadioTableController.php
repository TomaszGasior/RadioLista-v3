<?php

namespace App\Controller;

use App\Entity\RadioTable;
use App\Entity\User;
use App\Export\RadioTableExporterProvider;
use App\Form\RadioTableColumnsType;
use App\Form\RadioTableCreateType;
use App\Form\RadioTableRemoveType;
use App\Form\RadioTableSettingsType;
use App\Repository\RadioStationRepository;
use Doctrine\ORM\EntityManagerInterface;
use RuntimeException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class RadioTableController extends AbstractController
{
    public function __construct(private EntityManagerInterface $entityManager) {}

    #[Route(['pl' => '/wykaz/{id}', 'en' => '/list/{id}'], name: 'radio_table.show')]
    #[IsGranted('RADIO_TABLE_SHOW', subject: 'radioTable', statusCode: 404)]
    public function show(RadioTable $radioTable, RadioStationRepository $radioStationRepository): Response
    {
        $radioStations = $radioStationRepository->findForRadioTable($radioTable);

        return $this->render('radio_table/show.html.twig', [
            'radio_table' => $radioTable,
            'radio_stations' => $radioStations,
        ]);
    }

    #[Route(['pl' => '/utworz-wykaz', 'en' => 'create-list'], name: 'radio_table.create')]
    #[IsGranted('IS_AUTHENTICATED_REMEMBERED')]
    public function create(Request $request, #[CurrentUser] User $user): Response
    {
        $form = $this->createForm(RadioTableCreateType::class, null, ['owner' => $user]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $radioTable = $form->getData();

            if (!$radioTable instanceof RadioTable) {
                throw new RuntimeException;
            }

            $this->entityManager->persist($radioTable);
            $this->entityManager->flush();

            $this->addFlash('notice', 'radio_table.create.notification.created');
            return $this->redirectToRoute('radio_table.show', ['id' => $radioTable->getId()]);
        }

        return $this->render('radio_table/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route(['pl' => '/wykaz/{id}/ustawienia', 'en' => '/list/{id}/settings'], name: 'radio_table.settings')]
    #[IsGranted('IS_AUTHENTICATED_REMEMBERED')]
    #[IsGranted('RADIO_TABLE_MODIFY', subject: 'radioTable', statusCode: 404)]
    public function settings(RadioTable $radioTable, Request $request): Response
    {
        $form = $this->createForm(RadioTableSettingsType::class, $radioTable);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->flush();

            $this->addFlash('notice', 'common.notification.saved_changes');
        }

        return $this->render('radio_table/settings.html.twig', [
            'form' => $form->createView(),
            'radio_table' => $radioTable,
        ]);
    }

    #[Route(['pl' => '/wykaz/{id}/kolumny', 'en' => '/list/{id}/columns'], name: 'radio_table.columns')]
    #[IsGranted('IS_AUTHENTICATED_REMEMBERED')]
    #[IsGranted('RADIO_TABLE_MODIFY', subject: 'radioTable', statusCode: 404)]
    public function columns(RadioTable $radioTable, Request $request): Response
    {
        $form = $this->createForm(RadioTableColumnsType::class, $radioTable);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->flush();

            $this->addFlash('notice', 'common.notification.saved_changes');
        }

        return $this->render('radio_table/columns.html.twig', [
            'form' => $form->createView(),
            'radio_table' => $radioTable,
        ]);
    }

    #[Route(
        ['pl' => '/wykaz/{id}/eksport/{_format}', 'en' => '/list/{id}/export/{_format}'],
        name: 'radio_table.download', requirements: ['_format' => 'csv|ods|xlsx|html|pdf']
    )]
    #[IsGranted('IS_AUTHENTICATED_REMEMBERED')]
    #[IsGranted('RADIO_TABLE_MODIFY', subject: 'radioTable', statusCode: 404)]
    public function download(RadioTable $radioTable, string $_format,
                             RadioStationRepository $radioStationRepository,
                             RadioTableExporterProvider $radioTableExporterProvider): Response
    {
        $radioStations = $radioStationRepository->findForRadioTable($radioTable);
        $exporter = $radioTableExporterProvider->getExporterForFileExtension($_format);

        $response = new Response(
            $exporter->render($radioTable, $radioStations)
        );
        $response->headers->set('Content-Disposition', $response->headers->makeDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            str_replace(['/', '\\'], '', $radioTable->getName()) . '.' . $_format,
            date('Y-m-d_H-i-s') . '.' . $_format
        ));

        return $response;
    }

    #[Route(['pl' => '/wykaz/{id}/eksport', 'en' => '/list/{id}/export'], name: 'radio_table.export')]
    #[IsGranted('IS_AUTHENTICATED_REMEMBERED')]
    #[IsGranted('RADIO_TABLE_MODIFY', subject: 'radioTable', statusCode: 404)]
    public function export(RadioTable $radioTable): Response
    {
        return $this->render('radio_table/export.html.twig', [
            'radio_table' => $radioTable,
        ]);
    }

    #[Route(['pl' => '/wykaz/{id}/usun', 'en' => '/list/{id}/delete'], methods: ['POST'], name: 'radio_table.remove')]
    #[IsGranted('IS_AUTHENTICATED_REMEMBERED')]
    #[IsGranted('RADIO_TABLE_MODIFY', subject: 'radioTable', statusCode: 404)]
    public function remove(RadioTable $radioTable): Response
    {
        $this->entityManager->remove($radioTable);
        $this->entityManager->flush();

        $this->addFlash('notice', 'radio_table.remove.notification.removed');

        return $this->redirectToRoute('user.my_radio_tables');
    }
}
