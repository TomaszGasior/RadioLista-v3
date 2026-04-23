<?php

namespace App\Row;

use App\Model\Row;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag]
interface RowFactoryInterface
{
    public function create(object $object): Row;

    public function supports(object $object): bool;
}
