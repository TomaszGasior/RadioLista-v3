<?php

namespace App\Event;

use App\Entity\Multiplex;
use Symfony\Component\DependencyInjection\Attribute\Exclude;

#[Exclude]
readonly class MultiplexCreated
{
    public function __construct(public Multiplex $multiplex) {}
}
