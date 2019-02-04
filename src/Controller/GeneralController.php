<?php

namespace App\Controller;

use App\Form\ContactFormType;
use App\Form\RadioTableSearchType;
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
        $form = $this->createForm(RadioTableSearchType::class, null, [
            'action' => $this->generateUrl('search_radiotables'),
        ]);

        return $this->render('general/homepage.html.twig', [
            'last_updated_radiotables' => $radioTableRepository->findPublicOrderedByLastUpdateTime(10),
            'last_created_radiotables' => $radioTableRepository->findPublicOrderedByIdDesc(10),
            'search_form' => $form->createView(),
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
    public function contactForm(\Swift_Mailer $mailer, Request $request, $contactAddress): Response
    {
        $form = $this->createForm(ContactFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            $message = (new \Swift_Message($data['subject']))
                ->setFrom($data['email'])
                ->setTo($contactAddress)
                ->setBody($data['content'])
            ;

            if ($mailer->send($message)) {
                $this->addFlash('notice', 'Wiadomość została wysłana!');
            }
            else {
                $this->addFlash('error', 'Wystąpił błąd. Spróbuj ponownie później.');
            }
        }

        return $this->render('general/contact.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/wszystkie-wykazy/{sorting}", name="all_radiotables", requirements={"sorting": "1|2|3"},
     *     condition="request.query.get('a') == ''")
     */
    public function allRadioTables(RadioTableRepository $radioTableRepository, $sorting = 1): Response
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

        return $this->render('general/all_radiotables.html.twig', [
            'radiotables' => $radioTables,
        ]);
    }

    /**
     * @Route("/szukaj", name="search_radiotables")
     */
    public function searchRadioTables(RadioTableRepository $radioTableRepository, Request $request): Response
    {
        $form = $this->createForm(RadioTableSearchType::class);
        $form->handleRequest($request);

        $searchTerm = $form->get('s')->getData();

        if (!$searchTerm) {
            return $this->redirectToRoute('homepage');
        }

        $radioTables = $radioTableRepository->findPublicBySearchTerm($searchTerm);

        return $this->render('general/search_radiotables.html.twig', [
            'radiotables' => $radioTables,
            'search_term' => $searchTerm,
            'search_form' => $form->createView(),
        ]);
    }
}
