<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Faker\Generator;

abstract class AbstractEntityFixture extends Fixture
{
    use FixtureTrait;

    protected const ENTITIES_NUMBER = 5;

    abstract protected function createEntity(Generator $faker, int $i): object;

    public function load(ObjectManager $manager): void
    {
        $this->setupFaker();

        foreach (range(1, static::ENTITIES_NUMBER) as $i) {
            $entity = $this->createEntity(self::$faker, $i);

            $manager->persist($entity);
            $this->addReference(static::class . $i, $entity);
        }

        $this->disableRefreshDateEntityListeners($manager, get_class($entity));

        $manager->flush();
    }

    protected function getReferenceFrom(string $fixtureClass, int $i = null): object
    {
        if (null === $i) {
            $i = random_int(1, $fixtureClass::ENTITIES_NUMBER);
        }

        return $this->getReference($fixtureClass . $i);
    }
}
