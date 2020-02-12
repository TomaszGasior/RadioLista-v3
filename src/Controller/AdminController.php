<?php

namespace App\Controller;

use App\Repository\RadioTableRepository;
use App\Repository\UserRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @IsGranted("ROLE_ADMIN", statusCode=404)
 */
class AdminController extends AbstractController
{
    /**
     * @Route("/admin")
     * @Route("/admin/dziennik", name="admin.logs")
     */
    public function logs(string $logFilePath): Response
    {
        return $this->render('admin/logs.html.twig', [
            'logs' => is_file($logFilePath) ? file_get_contents($logFilePath) : '',
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
