<?php

namespace App\Validator;

use App\Util\ReflectionUtilsTrait;
use Symfony\Component\Validator\Constraints\Choice;
use Symfony\Component\Validator\Constraints\ChoiceValidator;

/**
 * @Annotation
 */
class ClassConstantsChoice extends Choice
{
    use ReflectionUtilsTrait;

    public $class;
    public $prefix;

    public function __construct($options = null)
    {
        parent::__construct($options);

        $this->choices = $this->getPrefixedConstantsOfClass($options['class'], $options['prefix']);
    }

    public function getDefaultOption(): ?string
    {
        return null;
    }

    public function getRequiredOptions(): array
    {
        return ['class', 'prefix'];
    }

    public function validatedBy(): string
    {
        return ChoiceValidator::class;
    }
}
