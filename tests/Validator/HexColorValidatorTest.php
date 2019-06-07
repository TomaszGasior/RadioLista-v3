<?php

namespace App\Tests\Validator;

use App\Validator\HexColor;
use App\Validator\HexColorValidator;
use Symfony\Component\Validator\ConstraintValidatorInterface;
use Symfony\Component\Validator\Test\ConstraintValidatorTestCase;

class HexColorValidatorTest extends ConstraintValidatorTestCase
{
    protected function createValidator(): ConstraintValidatorInterface
    {
        return new HexColorValidator;
    }

    /**
     * @dataProvider dataProvider
     */
    public function testHexColorValidator(string $value, bool $isValid): void
    {
        $constraint = new HexColor;
        $this->validator->validate($value, $constraint);

        if ($isValid) {
            $this->assertNoViolation();
        }
        else {
            $this->buildViolation($constraint->message)
                ->setParameter('{{ value }}', $value)
                ->assertRaised();
        }
    }

    public function dataProvider(): iterable
    {
        $validValues = [
            '#ff83A8',
            '#FF83A8',
            '#AEC',
            '#AAEECC',
        ];
        $invalidValues = [
            '#XYEECC',
            '#AEG',
            '#AECe',
            '#AECee',
            '#Ae',
            '#A',
            'FF83A8',
            'FF838',
            'FF88',
            'FF8',
            'AEC',
            'AC',
            'C',
        ];

        foreach ($validValues as $value) {
            yield [$value, true];
        }
        foreach ($invalidValues as $value) {
            yield [$value, false];
        }
    }
}
