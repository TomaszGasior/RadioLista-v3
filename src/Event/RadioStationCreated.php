<?php

namespace App\Event;

use App\Entity\RadioStation;
use Symfony\Component\DependencyInjection\Attribute\Exclude;

#[Exclude]
readonly class RadioStationCreated
{
    public function __construct(public RadioStation $radioStation) {}
}
