<?php

namespace App\Command;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Util\PasswordGeneratorTrait;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ChangeUserPassCommand extends Command
{
    use PasswordGeneratorTrait;

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

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        // There is no way to access APCu cache here. Don't warn about it.
        $this->entityManager->getClassMetadata(User::class)->cache = null;

        $name = $io->ask('Username');
        if (! $user = $this->userRepository->findOneByName($name)) {
            $io->error('There is no such user.');
            return 1;
        }

        $newPassword = $io->ask('New password', $this->getRandomPassword());
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

        $this->entityManager->flush($user);

        $newPasswordHash = $user->getPassword();

        $io->success([
            'Password changed.',
            sprintf(
                "Old password hash: %s\nNew password hash: %s",
                $oldPasswordHash, $newPasswordHash
            )
        ]);

        // Clear APCu cache after changing the password.
        $this->getApplication()->run(new ArrayInput(['command' => 'cache:clear']), $output);

        return 0;
    }
}
