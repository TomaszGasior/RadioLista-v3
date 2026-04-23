<?php

namespace App\Model;

use Symfony\Component\DependencyInjection\Attribute\Exclude;

#[Exclude]
readonly class RowEditRoute
{
    public function __construct(public string $name, public array $parameters) {}
}
