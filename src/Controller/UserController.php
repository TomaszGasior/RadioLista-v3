<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserRegisterType;
use App\Form\UserSettingsType;
use App\Repository\RadioTableRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    /**
     * @Route({"pl": "/profil/{name}", "en": "/profile/{name}"}, name="user.public_profile")
     * @IsGranted("USER_PUBLIC_PROFILE", subject="user", statusCode=404)
     */
    public function publicProfile(User $user, RadioTableRepository $radioTableRepository): Response
    {
        $radioTables = $radioTableRepository->findPublicOwnedByUser($user);

        return $this->render('user/public_profile.html.twig', [
            'user' => $user,
            'radio_tables' => $radioTables,
        ]);
    }

    /**
     * @Route({"pl": "/moje-wykazy", "en": "/my-lists"}, name="user.my_radio_tables")
     * @IsGranted("IS_AUTHENTICATED_REMEMBERED")
     */
    public function myRadioTables(RadioTableRepository $radioTableRepository): Response
    {
        $radioTables = $radioTableRepository->findAllOwnedByUser($this->getUser());

        return $this->render('user/my_radio_tables.html.twig', [
            'radio_tables' => $radioTables,
        ]);
    }

    /**
     * @Route({"pl": "/ustawienia-konta", "en": "/account-settings"}, name="user.my_account_settings")
     * @IsGranted("IS_AUTHENTICATED_REMEMBERED")
     */
    public function myAccountSettings(EntityManagerInterface $entityManager, Request $request): Response
    {
        $form = $this->createForm(UserSettingsType::class, $this->getUser());
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            if ($form->get('plainPassword')->getData()) {
                $this->addFlash('notice', 'user.settings.notification.changed_password');
            }
            else {
                $this->addFlash('notice', 'common.notification.saved_changes');
            }
        }

        return $this->render('user/my_account_settings.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route({"pl": "/rejestracja", "en": "register"}, name="user.register")
     */
    public function register(Request $request, EntityManagerInterface $entityManager): Response
    {
        if (null !== $this->getUser()) {
            return $this->redirectToRoute('homepage');
        }

        $user = new User;

        $form = $this->createForm(UserRegisterType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('notice', 'user.register.notification.registered');
            return $this->redirectToRoute('security.login');
        }

        return $this->render('user/register.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
