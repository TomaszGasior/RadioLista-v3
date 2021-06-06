<?php

namespace App\Validator;

use App\Entity\RadioStation;
use App\Util\DabChannelsTrait;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;

class DabChannelValidator extends ConstraintValidator
{
    use DabChannelsTrait;

    public function validate($value, Constraint $constraint)
    {
        if (!$constraint instanceof DabChannel) {
            throw new UnexpectedTypeException($constraint, DabChannel::class);
        }

        $radioStation = $this->context->getObject();
        if (!$radioStation instanceof RadioStation) {
            throw new UnexpectedTypeException($radioStation, RadioStation::class);
        }

        if (null === $value || '' === $value) {
            return;
        }

        if (!is_scalar($value) && !(is_object($value) && method_exists($value, '__toString'))) {
            throw new UnexpectedValueException($value, 'string');
        }

        $value = (string) $value;
        $dabChannels = $this->getDabChannelsWithFrequencies();

        $dabChannelIsKnown = isset($dabChannels[$value]);
        $dabChannelMatchesFrequency = ($dabChannels[$value] ?? false) === (string) $radioStation->getFrequency();

        if ($dabChannelIsKnown && $dabChannelMatchesFrequency) {
            return;
        }

        $this->context->buildViolation($constraint->message)
            ->setParameter('{{ value }}', $value)
            ->addViolation()
        ;
    }
}
