<?php

namespace App\Event;

use App\Entity\Multiplex;
use Symfony\Component\DependencyInjection\Attribute\Exclude;

#[Exclude]
readonly class MultiplexRemoved
{
    public function __construct(public Multiplex $multiplex) {}
}
