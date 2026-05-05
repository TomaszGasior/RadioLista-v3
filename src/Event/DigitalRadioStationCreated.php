<?php

namespace App\Event;

use App\Entity\DigitalRadioStation;
use Symfony\Component\DependencyInjection\Attribute\Exclude;

#[Exclude]
readonly class DigitalRadioStationCreated
{
    public function __construct(public DigitalRadioStation $digitalRadioStation) {}
}
