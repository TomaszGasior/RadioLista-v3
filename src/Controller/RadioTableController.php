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
     * @Route("/wykaz/{id}", name="radiotable.show")
     * @IsGranted("RADIOTABLE_SHOW", subject="radioTable", statusCode=404)
     */
    public function show(RadioTable $radioTable, RadioStationRepository $radioStationRepository): Response
    {
        $radioStations = $radioStationRepository->findForRadioTable($radioTable);

        return $this->render('radiotable/show.html.twig', [
            'radiotable' => $radioTable,
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
     * @IsGranted("IS_AUTHENTICATED_REMEMBERED")
     * @IsGranted("RADIOTABLE_MODIFY", subject="radioTable", statusCode=404)
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
            'form' => $form->createView(),
            'radiotable' => $radioTable,
        ]);
    }

    /**
     * @Route("/eksport-wykazu/{id}/{_format}", name="radiotable.download", requirements={"_format": "csv|html|pdf"})
     * @IsGranted("IS_AUTHENTICATED_REMEMBERED")
     * @IsGranted("RADIOTABLE_MODIFY", subject="radioTable", statusCode=404)
     */
    public function download(RadioTable $radioTable, string $_format, Pdf $pdfRenderer,
                             RadioStationRepository $radioStationRepository): Response
    {
        $radioStations = $radioStationRepository->findForRadioTable($radioTable);

        $templateVars = [
            'radiotable' => $radioTable,
            'radiostations' => $radioStations,
        ];

        switch ($_format) {
            case 'html':
                $content = $this->renderView('radiotable/standalone.html.twig', $templateVars);
                break;
            case 'csv':
                $content = $this->renderView('radiotable/table/radiotable.csv.twig', $templateVars);
                break;
            case 'pdf':
                $content = $pdfRenderer->getOutputFromHtml(
                    $this->renderView('radiotable/standalone.html.twig', $templateVars)
                );
                break;
        }

        $response = new Response($content);

        $response->headers->set('Content-Disposition', $response->headers->makeDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            $radioTable->getName() . '.' . $_format, date('Y-m-d_H-i-s') . '.' . $_format
        ));

        return $response;
    }

    /**
     * @Route("/eksport-wykazu/{id}", name="radiotable.export")
     * @IsGranted("IS_AUTHENTICATED_REMEMBERED")
     * @IsGranted("RADIOTABLE_MODIFY", subject="radioTable", statusCode=404)
     */
    public function export(RadioTable $radioTable): Response
    {
        return $this->redirectToRoute('radiotable.settings', [
            'id' => $radioTable->getId(),
            '_fragment' => 'export',
        ]);
    }

    /**
     * @Route("/usun-wykaz/{id}", name="radiotable.remove")
     * @ParamConverter("radioStationToRemove", class="stdClass")
     * @IsGranted("IS_AUTHENTICATED_REMEMBERED")
     * @IsGranted("RADIOTABLE_MODIFY", subject="radioTable", statusCode=404)
     */
    public function remove(RadioTable $radioTable, Request $request, EntityManagerInterface $entityManager,
                           RadioStation $radioStationToRemove = null): Response
    {
        // This action handles both radiotable removing and radiostation removing.

        $form_RadioTable = $this->createForm(RadioTableRemoveType::class);
        $form_RadioTable->handleRequest($request);

        $form_RadioStation = $this->createForm(RadioStationRemoveType::class,
            $radioStationToRemove ? ['chosenToRemove' => [$radioStationToRemove]] : null,
            ['radiotable' => $radioTable]
        );
        $form_RadioStation->handleRequest($request);

        if ($form_RadioTable->isSubmitted() && $form_RadioTable->isValid()) {
            $confirmed = (true === $form_RadioTable->getData()['confirm']);

            if ($confirmed) {
                $entityManager->remove($radioTable);
                $entityManager->flush();

                $this->addFlash('notice', 'Wykaz został bezpowrotnie usunięty.');
                return $this->redirectToRoute('user.my_radiotables');
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
                return $this->redirectToRoute('radiotable.remove', [
                    'id' => $radioTable->getId(),
                ]);
            }
        }

        return $this->render('radiotable/remove.html.twig', [
            'form_radiotable' => $form_RadioTable->createView(),
            'form_radiostation' => $form_RadioStation->createView(),
            'radiotable' => $radioTable,
        ]);
    }
}
