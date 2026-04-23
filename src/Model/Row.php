<?php

namespace App\Model;

use App\Entity\Embeddable\RadioStation\Appearance;
use App\Entity\Embeddable\RadioStation\Rds;
use App\Entity\Enum\RadioStation\DabChannel;
use App\Entity\Enum\RadioStation\Polarization;
use App\Entity\Enum\RadioStation\Quality;
use App\Entity\Enum\RadioStation\Reception;
use App\Entity\Enum\RadioStation\Type;
use Symfony\Component\DependencyInjection\Attribute\Exclude;

#[Exclude]
readonly class Row
{
    public function __construct(
        public string $name,

        public ?string $radioGroup,

        public ?string $country,

        public ?string $region,

        public string $frequency,

        public ?string $location,

        public ?string $power,

        public ?Polarization $polarization,

        public ?string $multiplex,

        public ?DabChannel $dabChannel,

        public ?int $distance,

        public ?int $maxSignalLevel,

        public Reception $reception,

        public ?int $privateNumber,

        public ?string $firstLogDate,

        public Quality $quality,

        public ?Type $type,

        public ?Rds $rds,

        public ?string $rdsPi,

        public ?string $comment,

        public ?string $externalAnchor,

        public Appearance $appearance,

        public RowEditRoute $rowEditRoute,
    ) {}
}
