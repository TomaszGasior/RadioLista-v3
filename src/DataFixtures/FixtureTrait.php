<?php

namespace App\DataFixtures;

use App\Util\ReflectionUtilsTrait;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Faker\Factory;
use Faker\Generator;
use Faker\Provider\Base;

trait FixtureTrait
{
    use ReflectionUtilsTrait;

    static private $faker;

    private function disableRefreshDateEntityListeners(ObjectManager $manager, string $entityClass): void
    {
        $entityListeners = $manager->getClassMetadata($entityClass)->entityListeners;

        array_walk($entityListeners, function(&$listeners){
            $listeners = array_filter($listeners, function($callback){
                return !preg_match('/refresh.*(Date|Time).*/', $callback['method']);
            });
        });

        $manager->getClassMetadata($entityClass)->entityListeners = $entityListeners;
    }

    private function setupFaker()
    {
        if (self::$faker) {
            return;
        }
        self::$faker = $faker = Factory::create('pl_PL');

        $faker->addProvider(new class($faker) extends Base
        {
            use ReflectionUtilsTrait;

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
                return '<p>' . implode('</p><p>', $paragraphs) . '</p>';
            }

            public function randomConstantFromClass($class, $prefix)
            {
                return $this->randomConstantsFromClass($class, $prefix)[0];
            }

            public function randomConstantsFromClass($class, $prefix): array
            {
                $values = $this->getPrefixedConstantsOfClass($class, $prefix);

                return $this->randomElements(
                    $values,
                    rand(ceil(count($values) * 0.25), count($values))
                );
            }
        });
    }
}
