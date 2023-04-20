<?php

namespace App\DataFixtures;

use App\Entity\Enum\RadioTable\Column;
use BackedEnum;
use DateTime;
use DateTimeImmutable;
use DateTimeInterface;
use Faker\Factory;
use Faker\Generator;

/**
 * @method self optional()
 */
class Faker extends Generator
{
    public function __construct()
    {
        // The way how this class is implemented is not correct in context of Faker's design
        // but it's simpler to maintain and makes IDE autocompletion working.
        $this->providers = Factory::create('pl_PL')->getProviders();

        parent::__construct();
    }

    /**
     * @template T of BackedEnum
     * @param class-string<T> $enumFqcn
     * @return T
     */
    public function randomEnum(string $enumFqcn): BackedEnum
    {
        return $this->randomElement($enumFqcn::cases());
    }

    public function dateTimeImmutableBetween(string|DateTimeInterface $startDate = '-30 years',
                                             string|DateTimeInterface $endDate = 'now'): DateTimeImmutable
    {
        if ($startDate instanceof DateTimeImmutable) {
            $startDate = DateTime::createFromImmutable($startDate);
        }
        if ($endDate instanceof DateTimeImmutable) {
            $endDate = DateTime::createFromImmutable($endDate);
        }

        return DateTimeImmutable::createFromMutable($this->dateTimeBetween($startDate, $endDate));
    }

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

    public function HTMLDescription(): string
    {
        $paragraphs = $this->paragraphs(3);

        return '<p>' . implode('</p><p>', $paragraphs) . '</p>';
    }

    /**
     * @return Column[]
     */
    public function columns(): array
    {
        $columns = $this->randomElements(Column::cases(), $this->numberBetween(2, count(Column::cases())));

        if (!in_array(Column::FREQUENCY, $columns)) {
            $columns[] = Column::FREQUENCY;
        }
        if (!in_array(Column::NAME, $columns)) {
            $columns[] = Column::NAME;
        }

        return $this->shuffleArray($columns);
    }
}
