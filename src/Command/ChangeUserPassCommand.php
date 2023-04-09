<?php

namespace App\Command;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Util\PasswordGenerator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ChangeUserPassCommand extends Command
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private UserRepository $userRepository,
        private UserPasswordHasherInterface $passwordEncoder,
        private ValidatorInterface $validator,
        private PasswordGenerator $passwordGenerator,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setName('app:change-user-pass')
            ->setDescription('Change user\'s password')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $name = $io->ask('Username');
        $user = $this->userRepository->findOneByName($name);
        if (!$user) {
            $io->error('There is no such user.');
            return 1;
        }

        $newPassword = $io->ask('New password', $this->passwordGenerator->getRandomPassword());
        if (!$newPassword) {
            $io->error('Password cannot be empty.');
            return 1;
        }

        $oldPasswordHash = $user->getPassword();
        $newPasswordHash = $this->passwordEncoder->hashPassword($user, $newPassword);

        $user->setPassword($newPasswordHash);

        $errors = $this->validator->validate($user);
        if ($errors->count() > 0) {
            $io->error((string) $errors);
            return 1;
        }

        $this->entityManager->flush();

        $io->success([
            'Password changed.',
            sprintf(
                "Old password hash: %s\nNew password hash: %s",
                $oldPasswordHash, $newPasswordHash
            )
        ]);

        // Clear Doctrine's second level cache.
        $this->getApplication()->run(new ArrayInput(['command' => 'cache:clear']), $output);

        return 0;
    }
}
