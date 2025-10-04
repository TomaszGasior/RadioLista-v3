<?php

namespace App\Event;

use App\Entity\RadioStation;
use Symfony\Component\DependencyInjection\Attribute\Exclude;

#[Exclude]
readonly class RadioStationRemoved
{
    public function __construct(public RadioStation $radioStation) {}
}
