<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserRegisterType;
use App\Form\UserSettingsType;
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
    public function publicProfile(User $user, RadioTableRepository $radioTableRepository): Response
    {
        $radioTables = $radioTableRepository->findPublicOwnedByUser($user);

        return $this->render('user/public_profile.html.twig', [
            'user'=> $user,
            'radiotables' => $radioTables,
        ]);
    }

    /**
     * @Route("/moje-wykazy", name="user.my_radiotables")
     * @IsGranted("IS_AUTHENTICATED_REMEMBERED")
     */
    public function myRadioTables(RadioTableRepository $radioTableRepository): Response
    {
        $radioTables = $radioTableRepository->findAllOwnedByUser($this->getUser());

        return $this->render('user/my_radiotables.html.twig', [
            'radiotables' => $radioTables,
        ]);
    }

    /**
     * @Route("/ustawienia-konta", name="user.my_account_settings")
     * @IsGranted("IS_AUTHENTICATED_REMEMBERED")
     */
    public function myAccountSettings(EntityManagerInterface $entityManager, Request $request): Response
    {
        $form = $this->createForm(UserSettingsType::class, $this->getUser());
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            if ($form->get('plainPassword')->getData()) {
                $this->addFlash('notice', 'Nowe hasło zostało zapisane.');
            }
            else {
                $this->addFlash('notice', 'Zmiany zostały zapisane.');
            }
        }

        return $this->render('user/my_account_settings.html.twig', [
            'form' => $form->createView(),
        ]);
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

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('notice', 'Twoje konto zostało utworzone.');
            return $this->redirectToRoute('security.login');
        }

        return $this->render('user/register.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
