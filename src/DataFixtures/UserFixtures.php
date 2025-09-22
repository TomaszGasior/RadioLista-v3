<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Util\ReflectionUtilsTrait;
use Symfony\Component\DependencyInjection\Attribute\When;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[When('dev')]
class UserFixtures extends AbstractEntityFixture
{
    use ReflectionUtilsTrait;

    protected const ENTITIES_NUMBER = 100;

    public function __construct(private Faker $faker, private UserPasswordHasherInterface $passwordEncoder) {}

    protected function createEntity(int $i): object
    {
        $user = new User(
            name: match ($i) {
                1 => 'radiolista',
                default => $this->faker->unique()->username(),
            },
        );

        $user->setPublicProfile($this->faker->boolean(75));

        if (1 === $i) {
            $user->setPublicProfile(true);
            $this->setPrivateFieldOfObject($user, 'admin', true);
        }

        $user->setPassword($this->passwordEncoder->hashPassword($user, $user->getName()));
        $user->setAboutMe($this->faker->optional()->HTMLDescription());

        $registerDate = $this->faker->dateTimeImmutableBetween('2012-07-01', '-1 year');
        $this->setPrivateFieldOfObject($user, 'registerDate', $registerDate);
        $this->setPrivateFieldOfObject($user, 'lastActivityDate', $this->faker->dateTimeImmutableBetween($registerDate, 'now'));

        return $user;
    }
}
