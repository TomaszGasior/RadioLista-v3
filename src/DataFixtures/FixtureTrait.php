<?php

namespace App\DataFixtures;

use App\Util\Data\DabChannelsTrait;
use App\Util\ReflectionUtilsTrait;
use BackedEnum;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Faker\Factory;
use Faker\Generator;
use Faker\Provider\Base;

trait FixtureTrait
{
    use ReflectionUtilsTrait;

    static private $faker;

    private function setupFaker(): void
    {
        if (self::$faker) {
            return;
        }
        self::$faker = $faker = Factory::create('pl_PL');

        $faker->addProvider(new class($faker) extends Base
        {
            use DabChannelsTrait;
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
                    'Supernova',
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

            public function multiplex(): string
            {
                // https://radiopolska.pl/wykaz/mux/radio
                return $this->randomElement([
                    'MUX Białystok',
                    'MUX Bydgoszcz',
                    'MUX Ciechanów',
                    'MUX Częstochowa',
                    'MUX DABCAST (Gdańsk)',
                    'MUX DABCAST (Katowice)',
                    'MUX DABCAST (Warszawa)',
                    'MUX DABCAST (Wrocław)',
                    'MUX Diecezji Tarnowskiej',
                    'MUX Emisja Testowa',
                    'MUX Gdańsk/Gdynia',
                    'MUX Giżycko',
                    'MUX Gorzów Wielkopolski',
                    'MUX Kalisz',
                    'MUX Katowice',
                    'MUX Kielce',
                    'MUX Koszalin',
                    'MUX Kraków',
                    'MUX Krosno',
                    'MUX Leszno',
                    'MUX LokalDAB',
                    'MUX Lublin',
                    'MUX Olsztyn',
                    'MUX Opole',
                    'MUX Poznań',
                    'MUX PR (Białystok)',
                    'MUX PR (Bydgoszcz)',
                    'MUX PR (Gdańsk)',
                    'MUX PR (Katowice)',
                    'MUX PR (Kielce)',
                    'MUX PR (Koszalin)',
                    'MUX PR (Kraków)',
                    'MUX PR (Lublin)',
                    'MUX PR (Olsztyn)',
                    'MUX PR (Opole)',
                    'MUX PR (Poznań)',
                    'MUX PR (Rzeszów)',
                    'MUX PR (Szczecin)',
                    'MUX PR (Warszawa)',
                    'MUX PR (Wrocław)',
                    'MUX PR (Zielona Góra)',
                    'MUX PR (Łódź)',
                    'MUX Radia Andrychów',
                    'MUX Radia Bielsko',
                    'MUX Radom',
                    'MUX Rzeszów',
                    'MUX Siedlce',
                    'MUX Szczecin',
                    'MUX Słupsk',
                    'MUX Tarnów',
                    'MUX Toruń',
                    'MUX TRANSMISJA',
                    'MUX Warszawa 1',
                    'MUX Warszawa 2',
                    'MUX Warszawa 3',
                    'MUX Wałbrzych',
                    'MUX Wrocław',
                    'MUX Zamość',
                    'MUX Zielona Góra',
                    'MUX Łomża',
                    'MUX Łowicz',
                    'MUX Łódź',
                    'MUX-R1',
                    'MUX-R2',
                ]);
            }

            public function dabChannelWithFrequency(): array
            {
                $dabChannels = $this->getDabChannelsWithFrequencies();

                return $this->randomElement(
                    array_map(
                        function($key, $value) { return [$key, $value]; },
                        array_keys($dabChannels),
                        $dabChannels
                    )
                );
            }

            public function HTMLDescription(): string
            {
                $paragraphs = $this->generator->paragraphs(3);

                return '<p>' . implode('</p><p>', $paragraphs) . '</p>';
            }

            public function randomEnum(string $enumFqcn): BackedEnum
            {
                return $this->randomElement($enumFqcn::cases());
            }

            public function randomConstantFromClass($class, $prefix): mixed
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
