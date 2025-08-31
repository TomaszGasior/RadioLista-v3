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
        // This is required for data fixtures with big amount of entities,
        // like radio station fixtures.
        ini_set('memory_limit', '600M');

        foreach (range(1, static::ENTITIES_NUMBER) as $i) {
            $entity = $this->createEntity($i);

            $manager->persist($entity);
            $this->referenceRepository->addReference('_'.$i, $entity);
        }

        $manager->flush();
    }

    /**
     * @template T of object
     * @param class-string<T> $entityClass
     * @return T
     */
    protected function getEntity(string $entityClass, ?int $i = null): object
    {
        $references = $this->referenceRepository->getReferencesByClass()[$entityClass];
        $key = null === $i ? array_rand($references) : ('_'.$i);

        return $this->referenceRepository->getReference($key, $entityClass);
    }
}
