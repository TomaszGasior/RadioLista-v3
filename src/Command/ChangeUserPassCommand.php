<?php

namespace App\Command;

use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ChangeUserPassCommand extends Command
{
    protected static $defaultName = 'app:change-user-pass';

    private $entityManager;
    private $userRepository;
    private $validator;

    public function __construct(EntityManagerInterface $entityManager,
                                UserRepository $userRepository,
                                ValidatorInterface $validator)
    {
        parent::__construct();

        $this->entityManager = $entityManager;
        $this->userRepository = $userRepository;
        $this->validator = $validator;
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Change user\'s password')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        $io = new SymfonyStyle($input, $output);

        $name = $io->ask('Username');

        if (! $user = $this->userRepository->findOneByName($name)) {
            $io->error('There is no such user.');
            return;
        }

        $newPassword = $io->ask('New password');
        if (! $newPassword) {
            $io->error('Password cannot be empty.');
            return;
        }

        $oldPasswordHash = $user->getPassword();

        $user->setPlainPassword($newPassword);
        $errors = $this->validator->validate($user, null, 'RedefinePassword');
        if ($errors->count() > 0) {
            $io->error((string) $errors);
            return;
        }

        $this->entityManager->flush($user);

        $newPasswordHash = $user->getPassword();

        $io->success([
            'Password changed.',
            sprintf(
                "Old password hash: %s\nNew password hash: %s",
                $oldPasswordHash, $newPasswordHash
            )
        ]);
    }
}
