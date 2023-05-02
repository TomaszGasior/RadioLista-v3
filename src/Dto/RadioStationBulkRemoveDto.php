<?php

namespace App\Dto;

use App\Entity\RadioStation;
use Symfony\Component\Validator\Constraints as Assert;

class RadioStationBulkRemoveDto
{
    /** @var RadioStation[] */
    #[Assert\NotBlank(message: 'radio_station.at_least_one_selected')]
    public array $radioStationsToRemove = [];
}
