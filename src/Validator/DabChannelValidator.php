<?php

namespace App\Validator;

use App\Entity\Enum\RadioStation\DabChannel as DabChannelEnum;
use App\Entity\RadioStation;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;

class DabChannelValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint)
    {
        if (!$constraint instanceof DabChannel) {
            throw new UnexpectedTypeException($constraint, DabChannel::class);
        }

        $radioStation = $this->context->getObject();
        if (!$radioStation instanceof RadioStation) {
            throw new UnexpectedTypeException($radioStation, RadioStation::class);
        }

        if (null === $value) {
            return;
        }

        if (!$value instanceof DabChannelEnum) {
            throw new UnexpectedValueException($value, DabChannelEnum::class);
        }

        $dabChannel = $value;

        $dabChannelMatchesFrequency = $dabChannel->getFrequency() === $radioStation->getFrequency();
        if ($dabChannelMatchesFrequency) {
            return;
        }

        $this->context->buildViolation($constraint->message)
            ->setParameter('{{ value }}', $dabChannel->value)
            ->addViolation()
        ;
    }
}
