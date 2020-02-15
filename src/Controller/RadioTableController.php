<?php

namespace App\Controller;

use App\Entity\RadioStation;
use App\Entity\RadioTable;
use App\Form\RadioStationRemoveType;
use App\Form\RadioTableCreateType;
use App\Form\RadioTableRemoveType;
use App\Form\RadioTableSettingsType;
use App\Repository\RadioStationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Snappy\Pdf;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Routing\Annotation\Route;

class RadioTableController extends AbstractController
{
    /**
     * @Route("/wykaz/{id}", name="radio_table.show")
     * @IsGranted("RADIO_TABLE_SHOW", subject="radioTable", statusCode=404)
     */
    public function show(RadioTable $radioTable, RadioStationRepository $radioStationRepository): Response
    {
        $radioStations = $radioStationRepository->findForRadioTable($radioTable);

        return $this->render('radio_table/show.html.twig', [
            'radio_table' => $radioTable,
            'radiostations' => $radioStations,
        ]);
    }

    /**
     * @Route("/utworz-wykaz", name="radio_table.create")
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
            return $this->redirectToRoute('user.my_radio_tables');
        }

        return $this->render('radio_table/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/wykaz/{id}/ustawienia", name="radio_table.settings")
     * @IsGranted("IS_AUTHENTICATED_REMEMBERED")
     * @IsGranted("RADIO_TABLE_MODIFY", subject="radioTable", statusCode=404)
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

        return $this->render('radio_table/settings.html.twig', [
            'form' => $form->createView(),
            'radio_table' => $radioTable,
        ]);
    }

    /**
     * @Route("/wykaz/{id}/eksport/{_format}", name="radio_table.download", requirements={"_format": "csv|html|pdf"})
     * @IsGranted("IS_AUTHENTICATED_REMEMBERED")
     * @IsGranted("RADIO_TABLE_MODIFY", subject="radioTable", statusCode=404)
     */
    public function download(RadioTable $radioTable, string $_format, Pdf $pdfRenderer,
                             RadioStationRepository $radioStationRepository): Response
    {
        $radioStations = $radioStationRepository->findForRadioTable($radioTable);

        $templateVars = [
            'radio_table' => $radioTable,
            'radiostations' => $radioStations,
        ];

        switch ($_format) {
            case 'html':
                $content = $this->renderView('radio_table/standalone.html.twig', $templateVars);
                break;
            case 'csv':
                $content = $this->renderView('radio_table/table/radio_table.csv.twig', $templateVars);
                break;
            case 'pdf':
                $content = $pdfRenderer->getOutputFromHtml(
                    $this->renderView('radio_table/standalone.html.twig', $templateVars)
                );
                break;
        }

        $response = new Response($content);

        $response->headers->set('Content-Disposition', $response->headers->makeDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            str_replace(['/', '\\'], '', $radioTable->getName()) . '.' . $_format,
            date('Y-m-d_H-i-s') . '.' . $_format
        ));

        return $response;
    }

    /**
     * @Route("/wykaz/{id}/eksport", name="radio_table.export")
     * @IsGranted("IS_AUTHENTICATED_REMEMBERED")
     * @IsGranted("RADIO_TABLE_MODIFY", subject="radioTable", statusCode=404)
     */
    public function export(RadioTable $radioTable): Response
    {
        return $this->redirectToRoute('radio_table.settings', [
            'id' => $radioTable->getId(),
            '_fragment' => 'export',
        ]);
    }

    /**
     * This action handles both radio table removing and radiostation removing.
     *
     * @Route("/wykaz/{id}/usun", name="radio_table.remove")
     * @ParamConverter("radioStationToRemove", class="stdClass")
     * @IsGranted("IS_AUTHENTICATED_REMEMBERED")
     * @IsGranted("RADIO_TABLE_MODIFY", subject="radioTable", statusCode=404)
     */
    public function remove(RadioTable $radioTable, Request $request, EntityManagerInterface $entityManager,
                           RadioStation $radioStationToRemove = null): Response
    {
        $form_RadioTable = $this->createForm(RadioTableRemoveType::class);
        $form_RadioTable->handleRequest($request);

        $form_RadioStation = $this->createForm(RadioStationRemoveType::class,
            $radioStationToRemove ? ['chosenToRemove' => [$radioStationToRemove]] : null,
            ['radio_table' => $radioTable]
        );
        $form_RadioStation->handleRequest($request);

        if ($form_RadioTable->isSubmitted() && $form_RadioTable->isValid()) {
            $confirmed = (true === $form_RadioTable->getData()['confirm']);

            if ($confirmed) {
                $entityManager->remove($radioTable);
                $entityManager->flush();

                $this->addFlash('notice', 'Wykaz został bezpowrotnie usunięty.');
                return $this->redirectToRoute('user.my_radio_tables');
            }
            else {
                $this->addFlash('error', 'Pamiętaj: jeśli jesteś na samym dnie, głowa do góry, może być już tylko lepiej!');
            }
        }
        elseif ($form_RadioStation->isSubmitted() && $form_RadioStation->isValid()) {
            $chosenToRemove = $form_RadioStation->getData()['chosenToRemove'];

            if (count($chosenToRemove) > 0) {
                foreach ($chosenToRemove as $radioStation) {
                    $entityManager->remove($radioStation);
                }
                $entityManager->flush();

                $this->addFlash('notice', 'Wybrane stacje zostały usunięte.');

                // Redirect to after successful radiostations removing.
                // * Form needs to be reloaded to not display removed radiostations.
                // * URL needs to be changed to avoid 404 error if page was forwarded from RadioStationController.
                return $this->redirectToRoute('radio_table.remove', [
                    'id' => $radioTable->getId(),
                ]);
            }
        }

        return $this->render('radio_table/remove.html.twig', [
            'form_radio_table' => $form_RadioTable->createView(),
            'form_radiostation' => $form_RadioStation->createView(),
            'radio_table' => $radioTable,
        ]);
    }
}
