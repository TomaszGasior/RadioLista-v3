<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class DabChannel extends Constraint
{
    public $message = 'radio_station.dab_channel_invalid_for_frequency';
}
