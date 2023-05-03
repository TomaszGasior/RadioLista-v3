<?php

namespace App\Controller;

use App\Dto\RadioTableSearchDto;
use App\Enum\RadioTableListSorting;
use App\Form\RadioTableSearchType;
use App\Repository\RadioTableRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class GeneralController extends AbstractController
{
    public function __construct(private RadioTableRepository $radioTableRepository) {}

    #[Route(['pl' => '', 'en' => '/en'], name: 'homepage')]
    public function homepage(): Response
    {
        $form = $this->createForm(RadioTableSearchType::class, null, [
            'action' => $this->generateUrl('search_radio_tables'),
        ]);

        return $this->render('general/homepage.html.twig', [
            'last_updated_radio_tables' => $this->radioTableRepository->findPublicOrderedByLastUpdateTime(12),
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
    public function allRadioTables(RadioTableListSorting $sorting = RadioTableListSorting::COUNT): Response
    {
        $radioTables = match ($sorting) {
            RadioTableListSorting::COUNT => $this->radioTableRepository->findPublicOrderedByRadioStationsCount(),
            RadioTableListSorting::LAST_UPDATE_TIME => $this->radioTableRepository->findPublicOrderedByLastUpdateTime(),
            RadioTableListSorting::FREQUENCY_UNIT => $this->radioTableRepository->findPublicOrderedByFrequencyUnit(),
        };

        return $this->render('general/all_radio_tables.html.twig', [
            'radio_tables' => $radioTables,
            'all_sortings' => RadioTableListSorting::cases(),
            'current_sorting' => $sorting,
        ]);
    }

    #[Route(['pl' => '/szukaj', 'en' => '/search'], name: 'search_radio_tables')]
    public function searchRadioTables(Request $request): Response
    {
        $data = new RadioTableSearchDto;

        $form = $this->createForm(RadioTableSearchType::class, $data);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $radioTables = $this->radioTableRepository->findPublicBySearchTerm($data->searchTerm);
        }

        return $this->render('general/search_radio_tables.html.twig', [
            'radio_tables' => $radioTables ?? [],
            'search_term' => $data->searchTerm,
            'search_form' => $form->createView(),
        ]);
    }
}
