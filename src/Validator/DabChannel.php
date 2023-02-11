<?php

namespace App\Validator;

use Attribute;
use Symfony\Component\Validator\Constraint;

#[Attribute]
class DabChannel extends Constraint
{
    public $message = 'radio_station.dab_channel_invalid_for_frequency';
}
