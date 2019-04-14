<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Faker\Factory;
use Faker\Generator;
use Faker\Provider\Base;

abstract class AbstractFixture extends Fixture
{
    protected const ENTITIES_NUMBER = 5;

    static private $faker;

    abstract protected function createEntity(Generator $faker, int $i): object;

    public function load(ObjectManager $manager): void
    {
        $this->setupFaker();

        foreach (range(1, static::ENTITIES_NUMBER) as $i) {
            $entity = $this->createEntity(self::$faker, $i);

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

    private function setupFaker()
    {
        if (self::$faker) {
            return;
        }
        self::$faker = $faker = Factory::create('pl_PL');

        $faker->addProvider(new class($faker) extends Base
        {
            public function radioStation(): string
            {
                return $this->randomElement([
                    'Polskie Radio Jedynka',
                    'Polskie Radio Dwójka',
                    'Polskie Radio Trójka',
                    'Polskie Radio Czwórka',
                    'Polskie Radio 24',
                    'RMF FM',
                    'RMF Classic',
                    'RMF Maxxx',
                    'Zet',
                    'Chillizet',
                    'Meloradio',
                    'AntyRadio',
                    'Eska',
                    'Wawa',
                    'Vox FM',
                    'Plus',
                    'Tok FM',
                    'Złote Przeboje',
                    'Pogoda',
                    'Rock Radio',
                    'Muzo.FM',
                    'Maryja',
                ]);
            }

            public function radioGroup(): string
            {
                return $this->randomElement([
                    'Polskie Radio',
                    'Audytorium 17',
                    'Grupa RMF',
                    'Eurozet',
                    'Grupa Radiowa Time',
                    'Grupa Radiowa Agory',
                ]);
            }

            public function HTMLDescription(): string
            {
                $paragraphs = $this->generator->paragraphs(3);
                return '<p>' . implode($paragraphs, '</p><p>') . '</p>';
            }

            public function randomConstantFromClass($class, $prefix)
            {
                return $this->randomConstantsFromClass($class, $prefix)[0];
            }

            public function randomConstantsFromClass($class, $prefix)
            {
                $values = array_filter(
                    (new \ReflectionClass($class))->getConstants(),
                    function($constantName) use ($prefix){
                        return (strpos($constantName, $prefix) === 0);
                    },
                    ARRAY_FILTER_USE_KEY
                );

                return $this->randomElements(
                    $values,
                    rand(ceil(count($values) * 0.25), count($values))
                );
            }
        });
    }
}
