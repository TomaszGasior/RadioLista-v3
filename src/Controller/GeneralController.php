<?php

namespace App\Controller;

use App\Repository\RadioTableRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class GeneralController extends AbstractController
{
    /**
     * @Route("", name="homepage")
     * @Route("/strona-glowna")
     */
    public function homepage()
    {
        return $this->render('general/homepage.html.twig');
    }

    /**
     * @Route("/o-stronie", name="about_service")
     */
    public function aboutService()
    {
        return $this->render('general/about-service.html.twig');
    }

    /**
     * @Route("/regulamin", name="terms_of_service")
     */
    public function termsOfService()
    {
        return $this->render('general/terms-of-service.html.twig');
    }

    /**
     * @Route("/kontakt", name="contact")
     */
    public function contactForm()
    {
        return $this->render('general/contact.html.twig');
    }

    /**
     * @Route("/wszystkie-wykazy/{sorting}", name="all_radiotables", requirements={"sorting": "1|2|3"})
     */
    public function allRadioTables(RadioTableRepository $radioTableRepository, $sorting = 1)
    {
        switch ($sorting) {
            case 1:
                $radioTablesList = $radioTableRepository->findPublicOrderedByRadioStationsCount();
                break;
            case 2:
                $radioTablesList = $radioTableRepository->findPublicOrderedByLastUpdateTime();
                break;
            case 3:
                $radioTablesList = $radioTableRepository->findPublicOrderedByUseKhz();
                break;
        }

        return $this->render('general/radiotables-list.html.twig', [
            'radioTablesList' => $radioTablesList,
        ]);
    }

    /**
     * @Route("/szukaj", name="search_radiotables")
     */
    public function searchRadioTables()
    {
        return $this->render('general/radiotables-search.html.twig');
    }
}
