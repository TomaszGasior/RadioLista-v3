<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints\DateValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;

class YearMonthDateValidator extends DateValidator
{
    public const ALTERNATIVE_PATTERN = '/^(\d{4})(-(0[1-9]|10|11|12))?$/';

    public function validate($value, Constraint $constraint)
    {
        if (!$constraint instanceof YearMonthDate) {
            throw new UnexpectedTypeException($constraint, YearMonthDate::class);
        }

        if (null === $value || '' === $value) {
            return;
        }

        if (!is_scalar($value) && !(is_object($value) && method_exists($value, '__toString'))) {
            throw new UnexpectedValueException($value, 'string');
        }

        $value = (string) $value;

        if (preg_match(static::ALTERNATIVE_PATTERN, $value)) {
            return;
        }

        parent::validate($value, $constraint);
    }
}
