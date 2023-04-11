<?php

namespace App\Controller;

use App\Repository\RadioTableRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SitemapController extends AbstractController
{
    public function __construct(
        private RadioTableRepository $radioTableRepository,
        private UserRepository $userRepository,
    ) {}

    #[Route('/sitemap.{_format<xml>}')]
    public function sitemap(): Response
    {
        $radioTables = $this->radioTableRepository->findPublicOrderedByLastUpdateTime();
        $users = $this->userRepository->findAllWithPublicProfile();

        return $this->render('sitemap.xml.twig', [
            'radio_tables' => $radioTables,
            'users' => $users,
        ]);
    }
}
