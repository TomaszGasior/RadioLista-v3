<?php

namespace App\Controller;

use App\Renderer\RadioTablesListRenderer;
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
        return $this->render('general/homepage.html.twig', [
            'last_updated_radiotables' => $radioTableRepository->findPublicOrderedByLastUpdateTime(5),
            'last_created_radiotables' => $radioTableRepository->findPublicOrderedByIdDesc(5),
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
     * @Route("/wszystkie-wykazy/{sorting}", name="all_radiotables", requirements={"sorting": "1|2|3"})
     */
    public function allRadioTables(RadioTableRepository $radioTableRepository, $sorting = 1,
                                   RadioTablesListRenderer $radioTablesListRenderer): Response
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

        $radioTablesList = $radioTablesListRenderer->render(
            $radioTables,
            RadioTablesListRenderer::OPTION_SHOW_OWNER
        );

        return $this->render('general/all_radiotables.html.twig', [
            'radiotables_list' => $radioTablesList,
        ]);
    }

    /**
     * @Route("/szukaj", name="search_radiotables")
     */
    public function searchRadioTables(): Response
    {
        return $this->render('general/search_radiotables.html.twig');
    }
}
