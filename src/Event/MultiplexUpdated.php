<?php

namespace App\Event;

use App\Entity\Multiplex;
use Symfony\Component\DependencyInjection\Attribute\Exclude;

#[Exclude]
readonly class MultiplexUpdated
{
    public function __construct(public Multiplex $multiplex) {}
}
