<?php

namespace App\Tests\Validator;

use App\Entity\RadioStation;
use App\Validator\ClassConstantsChoice;
use Symfony\Component\Validator\ConstraintValidatorInterface;
use Symfony\Component\Validator\Constraints\Choice;
use Symfony\Component\Validator\Constraints\ChoiceValidator;
use Symfony\Component\Validator\Test\ConstraintValidatorTestCase;

class ClassConstantsChoiceTest extends ConstraintValidatorTestCase
{
    protected function createValidator(): ConstraintValidatorInterface
    {
        return new ChoiceValidator;
    }

    public function dataProvider(): iterable
    {
        $class = RadioStation::class;
        $validConstants = [
            RadioStation::POLARIZATION_HORIZONTAL,
            RadioStation::POLARIZATION_VERTICAL,
            RadioStation::POLARIZATION_CIRCULAR,
            RadioStation::POLARIZATION_VARIOUS,
            RadioStation::POLARIZATION_NONE,
        ];
        $constantsPrefix = 'POLARIZATION_';

        $testedValues = [
            RadioStation::POLARIZATION_HORIZONTAL,
            RadioStation::POLARIZATION_VERTICAL,
            RadioStation::POLARIZATION_CIRCULAR,
            RadioStation::POLARIZATION_VARIOUS,
            RadioStation::POLARIZATION_NONE,
            RadioStation::QUALITY_VERY_GOOD,
            RadioStation::QUALITY_GOOD,
            RadioStation::QUALITY_MIDDLE,
            RadioStation::QUALITY_BAD,
            RadioStation::QUALITY_VERY_BAD,
        ];

        foreach ($testedValues as $testedValue) {
            yield [$class, $constantsPrefix, $validConstants, $testedValue];
        }
    }

    /**
     * @dataProvider dataProvider
     */
    public function testClassConstantsChoiceConstraint($class, $constantsPrefix,
                                                       $validConstants, $value): void
    {
        $classConstantsChoiceConstraint = new ClassConstantsChoice(
            ['class' => $class, 'prefix' => $constantsPrefix]
        );
        $choiceConstraint = new Choice(['choices' => $validConstants]);

        $this->validator->validate($value, $classConstantsChoiceConstraint);
        $classConstantsChoiceViolation = $this->context->getViolations()[0] ?? null;

        $this->validator->validate($value, $choiceConstraint);
        $choiceViolation = $this->context->getViolations()[1] ?? null;

        $this->assertEquals($choiceViolation, $classConstantsChoiceViolation);
    }

    public function testThrowExceptionWithNoChoices(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessageMatches('/stdClass.*NON_EXISTENT_CONSTANT_PREFIX_38907412/');

        new ClassConstantsChoice(
            ['class' => \stdClass::class, 'prefix' => 'NON_EXISTENT_CONSTANT_PREFIX_38907412']
        );
    }
}
