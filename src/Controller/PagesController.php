<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class PagesController extends AbstractController
{
    /**
     * @Route("", name="homepage")
     * @Route("/strona-glowna")
     */
    public function homepage()
    {
        return $this->render('pages/homepage.html.twig');
    }

    /**
     * @Route("/o-stronie", name="about-service")
     */
    public function aboutService()
    {
        return $this->render('pages/about-service.html.twig');
    }

    /**
     * @Route("/regulamin", name="terms-of-service")
     */
    public function termsOfService()
    {
        return $this->render('pages/terms-of-service.html.twig');
    }

    /**
     * @Route("/kontakt", name="contact-form")
     */
    public function contactForm()
    {
        return $this->render('pages/contact.html.twig');
    }

    /**
     * @Route("/wszystkie-wykazy", name="radiotables-list")
     */
    public function radioTablesList()
    {
        return $this->render('pages/radiotables-list.html.twig');
    }

    /**
     * @Route("/szukaj", name="radiotables-search")
     */
    public function radioTablesSearch()
    {
        return $this->render('pages/radiotables-search.html.twig');
    }
}
