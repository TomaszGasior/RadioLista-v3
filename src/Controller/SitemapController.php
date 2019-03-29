<?php

namespace App\Controller;

use App\Repository\RadioTableRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SitemapController extends AbstractController
{
    /**
     * @Route("/sitemap.{_format<xml>}")
     */
    public function sitemap(RadioTableRepository $radioTableRepository): Response
    {
        $radioTables = $radioTableRepository->findPublicOrderedByLastUpdateTime();

        return $this->render('sitemap.xml.twig', [
            'radiotables' => $radioTables,
        ]);
    }
}
