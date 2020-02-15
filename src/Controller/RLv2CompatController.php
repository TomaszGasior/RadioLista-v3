<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class RLv2CompatController extends AbstractController
{
    /**
     * @Route("/wykaz")
     */
    public function radioTable(Request $request): Response
    {
        $radioTableId = $request->query->get('id');

        if (empty($radioTableId) or !is_numeric($radioTableId)) {
            throw $this->createNotFoundException();
        }

        return $this->redirectToRoute('radio_table.show', ['id' => $radioTableId], 301);
    }

    /**
     * @Route("/profil")
     */
    public function userPublicProfile(Request $request): Response
    {
        $username = $request->query->get('u');

        if (empty($username)) {
            throw $this->createNotFoundException();
        }

        return $this->redirectToRoute('user.public_profile', ['name' => $username], 301);
    }

    /**
     * @Route("/wszystkie-wykazy", condition="request.query.get('a') != ''")
     */
    public function allRadioTables(Request $request): Response
    {
        $sorting = $request->query->get('a');

        if (empty($sorting) or !is_numeric($sorting)) {
            $sorting = 1;
        }

        return $this->redirectToRoute('all_radio_tables', ['sorting' => $sorting], 301);
    }
}
