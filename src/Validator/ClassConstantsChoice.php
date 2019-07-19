<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraints\Choice;
use Symfony\Component\Validator\Constraints\ChoiceValidator;

/**
 * @Annotation
 */
class ClassConstantsChoice extends Choice
{
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

    private function getPrefixedConstantsOfClass($class, $constantsPrefix): array
    {
        return array_filter(
            (new \ReflectionClass($class))->getConstants(),
            function ($constantName) use ($constantsPrefix) {
                return (0 === strpos($constantName, $constantsPrefix));
            },
            ARRAY_FILTER_USE_KEY
        );
    }
}
