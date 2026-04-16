<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserRegisterType;
use App\Form\UserSettingsType;
use App\Repository\RadioTableRepository;
use Doctrine\ORM\EntityManagerInterface;
use RuntimeException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Security\Http\Attribute\IsCsrfTokenValid;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class UserController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private RadioTableRepository $radioTableRepository,
    ) {}

    #[Route(['pl' => '/profil/{name:user}', 'en' => '/profile/{name:user}'], name: 'user.public_profile')]
    #[IsGranted('USER_PUBLIC_PROFILE', subject: 'user', statusCode: 404)]
    public function publicProfile(User $user): Response
    {
        $radioTables = $this->radioTableRepository->findPublicOwnedByUser($user);

        return $this->render('user/public_profile.html.twig', [
            'user' => $user,
            'radio_tables' => $radioTables,
        ]);
    }

    #[Route(['pl' => '/moje-wykazy', 'en' => '/my-lists'], name: 'user.my_radio_tables')]
    #[IsGranted('IS_AUTHENTICATED_REMEMBERED')]
    public function myRadioTables(#[CurrentUser] User $user): Response
    {
        $radioTables = $this->radioTableRepository->findAllOwnedByUser($user);

        return $this->render('user/my_radio_tables.html.twig', [
            'radio_tables' => $radioTables,
        ]);
    }

    #[Route(['pl' => '/ustawienia-konta', 'en' => '/account-settings'], name: 'user.my_account_settings')]
    #[IsGranted('IS_AUTHENTICATED_REMEMBERED')]
    public function myAccountSettings(Request $request, #[CurrentUser] User $user): Response
    {
        $form = $this->createForm(UserSettingsType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->flush();

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

    #[Route(['pl' => '/rejestracja', 'en' => 'register'], name: 'user.register')]
    public function register(Request $request, #[CurrentUser] ?User $currentUser): Response
    {
        if (null !== $currentUser) {
            return $this->redirectToRoute('homepage');
        }

        $form = $this->createForm(UserRegisterType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $form->getData();

            if (!$user instanceof User) {
                throw new RuntimeException;
            }

            $this->entityManager->persist($user);
            $this->entityManager->flush();

            $this->addFlash('notice', 'user.register.notification.registered');
            return $this->redirectToRoute('security.login');
        }

        return $this->render('user/register.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route(['pl' => '/ustawienia-konta/usun', 'en' => '/account-settings/delete'], methods: ['POST'], name: 'user.remove')]
    #[IsGranted('IS_AUTHENTICATED_REMEMBERED')]
    #[IsCsrfTokenValid('remove-dialog')]
    public function remove(Security $security, #[CurrentUser] User $user): Response
    {
        $radioTables = $this->radioTableRepository->findAllOwnedByUser($user);

        $this->entityManager->remove($user);

        foreach ($radioTables as $radioTable) {
            // This is only required to update Doctrine's second level cache.
            $this->entityManager->remove($radioTable);
        }

        $this->entityManager->flush();
        $security->logout(false);

        $this->addFlash('notice', 'user.remove.notification.removed');

        return $this->redirectToRoute('homepage');
    }
}
