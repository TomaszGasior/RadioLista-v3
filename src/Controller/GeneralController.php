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
    #[Route(['pl' => '', 'en' => '/en'], name: 'homepage')]
    public function homepage(RadioTableRepository $radioTableRepository): Response
    {
        $form = $this->createForm(RadioTableSearchType::class, null, [
            'action' => $this->generateUrl('search_radio_tables'),
        ]);

        return $this->render('general/homepage.html.twig', [
            'last_updated_radio_tables' => $radioTableRepository->findPublicOrderedByLastUpdateTime(12),
            'search_form' => $form->createView(),
        ]);
    }

    #[Route(['pl' => '/o-stronie', 'en' => '/about'], name: 'about_service')]
    public function aboutService(): Response
    {
        return $this->render('general/about_service.html.twig');
    }

    #[Route('/regulamin', name: 'terms_of_service')]
    public function termsOfService(): Response
    {
        return $this->render('general/terms_of_service.html.twig');
    }

    #[Route(['pl' => '/kontakt', 'en' => '/contact'], name: 'contact')]
    public function contact(): Response
    {
        return $this->render('general/contact.html.twig');
    }

    #[Route(
        ['pl' => '/wszystkie-wykazy/{sorting}', 'en' => '/all-lists/{sorting}'],
        name: 'all_radio_tables', defaults: ['sorting' => 1]
    )]
    public function allRadioTables(RadioTableRepository $radioTableRepository,
                                   RadioTableListSorting $sorting): Response
    {
        $radioTables = match ($sorting) {
            RadioTableListSorting::COUNT => $radioTableRepository->findPublicOrderedByRadioStationsCount(),
            RadioTableListSorting::LAST_UPDATE_TIME => $radioTableRepository->findPublicOrderedByLastUpdateTime(),
            RadioTableListSorting::FREQUENCY_UNIT => $radioTableRepository->findPublicOrderedByFrequencyUnit(),
        };

        return $this->render('general/all_radio_tables.html.twig', [
            'radio_tables' => $radioTables,
        ]);
    }

    #[Route(['pl' => '/szukaj', 'en' => '/search'], name: 'search_radio_tables')]
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

enum RadioTableListSorting: int
{
    case COUNT = 1;
    case LAST_UPDATE_TIME = 2;
    case FREQUENCY_UNIT = 3;
}
