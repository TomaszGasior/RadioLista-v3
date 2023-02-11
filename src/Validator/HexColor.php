<?php

namespace App\Validator;

use Attribute;
use Symfony\Component\Validator\Constraint;

#[Attribute]
class HexColor extends Constraint
{
    public $message = 'radio_table.color_invalid_format';
}
