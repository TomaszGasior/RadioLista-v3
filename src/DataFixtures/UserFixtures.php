<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Util\ReflectionUtilsTrait;
use DateTimeImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\DependencyInjection\Attribute\When;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[When('dev')]
class UserFixtures extends Fixture
{
    use ReflectionUtilsTrait;

    public function __construct(
        #[Autowire('%kernel.project_dir%/fixtures/user.php')] private string $source,
        private UserPasswordHasherInterface $passwordEncoder,
    ) {}

    public function load(ObjectManager $manager): void
    {
        foreach (include $this->source as $id => $data) {
            [$name, $publicProfile, $aboutMe, $registerDate, $lastActivityDate] = $data;

            if ($id === 1) {
                $name = 'radiolista';
            }

            $user = (new User($name))
                ->setPublicProfile($publicProfile)
                ->setAboutMe($aboutMe)
            ;

            $user->setPassword($this->passwordEncoder->hashPassword($user, $name));

            $this->setPrivateFieldOfObject($user, 'registerDate', new DateTimeImmutable($registerDate));
            $this->setPrivateFieldOfObject($user, 'lastActivityDate', new DateTimeImmutable($lastActivityDate));

            if ($id === 1) {
                $this->setPrivateFieldOfObject($user, 'admin', true);
            }

            $this->addReference(User::class . $id, $user);
            $manager->persist($user);
        }

        $manager->flush();
    }
}
