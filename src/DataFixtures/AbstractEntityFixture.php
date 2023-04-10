<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

abstract class AbstractEntityFixture extends Fixture
{
    protected const ENTITIES_NUMBER = 5;

    abstract protected function createEntity(int $i): object;

    public function load(ObjectManager $manager): void
    {
        foreach (range(1, static::ENTITIES_NUMBER) as $i) {
            $entity = $this->createEntity($i);

            $manager->persist($entity);
            $this->addReference(static::class . $i, $entity);
        }

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
