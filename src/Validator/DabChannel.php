<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class DabChannel extends Constraint
{
    public $message = 'The value "{{ value }}" is not valid.';
}
