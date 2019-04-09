<?php

namespace App\Controller;

use App\Repository\RadioTableRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends AbstractController
{
    /**
     * @Route("/admin")
     */
    public function admin(): Response
    {
        return $this->redirectToRoute('admin.logs');
    }

    /**
     * @Route("/admin/dziennik", name="admin.logs")
     */
    public function logs(): Response
    {
        return $this->render('admin/logs.html.twig', [
        ]);
    }

    /**
     * @Route("/admin/wykazy", name="admin.radiotables")
     */
    public function radioTables(RadioTableRepository $radioTableRepository): Response
    {
        return $this->render('admin/radiotables.html.twig', [
            'radiotables' => $radioTableRepository->findAll(),
        ]);
    }

    /**
     * @Route("/admin/uzytkownicy", name="admin.users")
     */
    public function users(UserRepository $userRepository): Response
    {
        return $this->render('admin/users.html.twig', [
            'users' => $userRepository->findAll(),
        ]);
    }
}
