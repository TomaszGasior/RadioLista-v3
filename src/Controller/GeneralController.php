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
     * @Route("/o-stronie", name="about-service")
     */
    public function aboutService()
    {
        return $this->render('general/about-service.html.twig');
    }

    /**
     * @Route("/regulamin", name="terms-of-service")
     */
    public function termsOfService()
    {
        return $this->render('general/terms-of-service.html.twig');
    }

    /**
     * @Route("/kontakt", name="contact-form")
     */
    public function contactForm()
    {
        return $this->render('general/contact.html.twig');
    }

    /**
     * @Route("/wszystkie-wykazy/{sorting}", name="radiotables-list", requirements={"sorting": "1|2|3"})
     */
    public function radioTablesList(RadioTableRepository $radioTableRepository, $sorting = 1)
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
     * @Route("/szukaj", name="radiotables-search")
     */
    public function radioTablesSearch()
    {
        return $this->render('general/radiotables-search.html.twig');
    }
}
