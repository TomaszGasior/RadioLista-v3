<?php

namespace App\Controller;

use App\Repository\RadioTableRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SitemapController extends AbstractController
{
    /**
     * @Route("/sitemap.{_format<xml>}")
     */
    public function sitemap(RadioTableRepository $radioTableRepository,
                            UserRepository $userRepository): Response
    {
        $radioTables = $radioTableRepository->findPublicOrderedByLastUpdateTime();
        $users = $userRepository->findAllWithPublicProfile();

        return $this->render('sitemap.xml.twig', [
            'radio_tables' => $radioTables,
            'users' => $users,
        ]);
    }
}
