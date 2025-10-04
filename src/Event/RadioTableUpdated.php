<?php

namespace App\Event;

use App\Entity\RadioTable;
use Symfony\Component\DependencyInjection\Attribute\Exclude;

#[Exclude]
readonly class RadioTableUpdated
{
    public function __construct(public RadioTable $radioTable) {}
}
