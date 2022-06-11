<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class HexColor extends Constraint
{
    public $message = 'radio_table.color_invalid_format';
}
