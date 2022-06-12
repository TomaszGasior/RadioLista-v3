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
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ChangeUserPassCommand extends Command
{
    protected static $defaultName = 'app:change-user-pass';

    private $entityManager;
    private $userRepository;
    private $validator;
    private $passwordGenerator;

    public function __construct(EntityManagerInterface $entityManager,
                                UserRepository $userRepository,
                                ValidatorInterface $validator,
                                PasswordGenerator $passwordGenerator)
    {
        parent::__construct();

        $this->entityManager = $entityManager;
        $this->userRepository = $userRepository;
        $this->validator = $validator;
        $this->passwordGenerator = $passwordGenerator;
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Change user\'s password')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $name = $io->ask('Username');
        if (! $user = $this->userRepository->findOneByName($name)) {
            $io->error('There is no such user.');
            return 1;
        }

        $newPassword = $io->ask('New password', $this->passwordGenerator->getRandomPassword());
        if (! $newPassword) {
            $io->error('Password cannot be empty.');
            return 1;
        }

        $oldPasswordHash = $user->getPassword();

        $user->setPlainPassword($newPassword);
        $errors = $this->validator->validate($user, null, 'RedefinePassword');
        if ($errors->count() > 0) {
            $io->error((string) $errors);
            return 1;
        }

        $this->entityManager->flush();

        $newPasswordHash = $user->getPassword();

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
