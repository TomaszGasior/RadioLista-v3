<?php

namespace App\Controller;

use App\Form\RadioTableSearchType;
use App\Repository\RadioTableRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class GeneralController extends AbstractController
{
    /**
     * @Route("", name="homepage")
     * @Route("/strona-glowna")
     */
    public function homepage(RadioTableRepository $radioTableRepository): Response
    {
        $form = $this->createForm(RadioTableSearchType::class, null, [
            'action' => $this->generateUrl('search_radio_tables'),
        ]);

        return $this->render('general/homepage.html.twig', [
            'last_updated_radio_tables' => $radioTableRepository->findPublicOrderedByLastUpdateTime(10),
            'last_created_radio_tables' => $radioTableRepository->findPublicOrderedByIdDesc(10),
            'search_form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/o-stronie", name="about_service")
     */
    public function aboutService(): Response
    {
        return $this->render('general/about_service.html.twig');
    }

    /**
     * @Route("/regulamin", name="terms_of_service")
     */
    public function termsOfService(): Response
    {
        return $this->render('general/terms_of_service.html.twig');
    }

    /**
     * @Route("/kontakt", name="contact")
     */
    public function contactForm(): Response
    {
        return $this->render('general/contact.html.twig');
    }

    /**
     * @Route("/wszystkie-wykazy/{sorting<1|2|3>?1}", name="all_radio_tables",
     *        condition="request.query.get('a') == ''")
     */
    public function allRadioTables(RadioTableRepository $radioTableRepository, $sorting): Response
    {
        switch ($sorting) {
            case 1:
                $radioTables = $radioTableRepository->findPublicOrderedByRadioStationsCount();
                break;
            case 2:
                $radioTables = $radioTableRepository->findPublicOrderedByLastUpdateTime();
                break;
            case 3:
                $radioTables = $radioTableRepository->findPublicOrderedByUseKhz();
                break;
        }

        return $this->render('general/all_radio_tables.html.twig', [
            'radio_tables' => $radioTables,
        ]);
    }

    /**
     * @Route("/szukaj", name="search_radio_tables")
     */
    public function searchRadioTables(RadioTableRepository $radioTableRepository, Request $request): Response
    {
        $form = $this->createForm(RadioTableSearchType::class);
        $form->handleRequest($request);

        $searchTerm = $form->get('s')->getData();

        if (!$searchTerm) {
            return $this->redirectToRoute('homepage');
        }

        $radioTables = $radioTableRepository->findPublicBySearchTerm($searchTerm);

        return $this->render('general/search_radio_tables.html.twig', [
            'radio_tables' => $radioTables,
            'search_term' => $searchTerm,
            'search_form' => $form->createView(),
        ]);
    }
}
