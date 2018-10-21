<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserRegisterType;
use App\Renderer\RadioTablesListRenderer;
use App\Repository\RadioTableRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    /**
     * @Route("/profil/{name}", name="user.public_profile")
     * @IsGranted("USER_PUBLIC_PROFILE", subject="user", statusCode=404)
     */
    public function publicProfile(User $user, RadioTablesListRenderer $radioTablesListRenderer): Response
    {
        $radioTablesList = $radioTablesListRenderer->render(
            $user->getRadioTables(),
            null
        );

        return $this->render('user/public_profile.html.twig', [
            'user'             => $user,
            'radiotables_list' => $radioTablesList,
        ]);
    }

    /**
     * @Route("/moje-wykazy", name="user.my_radiotables")
     * @IsGranted("IS_AUTHENTICATED_REMEMBERED")
     */
    public function myRadioTables(RadioTableRepository $radioTableRepository,
                                  RadioTablesListRenderer $radioTablesListRenderer): Response
    {
        $radioTables = $radioTableRepository->findOwnedByUser($this->getUser());
        $radioTablesList = $radioTablesListRenderer->render(
            $radioTables,
            RadioTablesListRenderer::OPTION_SHOW_VISIBILITY | RadioTablesListRenderer::OPTION_SHOW_ACTIONS
        );

        return $this->render('user/my_radiotables.html.twig', [
            'radiotables_list' => $radioTablesList,
        ]);
    }

    /**
     * @Route("/ustawienia-konta", name="user.my_account_settings")
     * @IsGranted("IS_AUTHENTICATED_FULLY")
     */
    public function myAccountSettings(): Response
    {
        return $this->render('user/my_account_settings.html.twig');
    }

    /**
     * @Route("/rejestracja", name="user.register")
     * @Security("not is_granted('IS_AUTHENTICATED_REMEMBERED')", statusCode=400)
     */
    public function register(Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = new User;

        $form = $this->createForm(UserRegisterType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $entityManager->persist($user);
                $entityManager->flush();

                $this->addFlash('notice', 'Twoje konto zostało utworzone.');
                return $this->redirectToRoute('security.login');
            }
            else {
                $this->addFlash('error', $form->getErrors(true));
            }
        }

        return $this->render('user/register.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
