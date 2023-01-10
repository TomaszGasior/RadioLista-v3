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

    public function validColorProvider(): iterable
    {
        $values = [
            '#ff83A8',
            '#FF83A8',
            '#AEC',
            '#AAEECC',
        ];

        foreach ($values as $value) {
            yield $value => [$value];
        }
    }

    public function invalidColorProvider(): iterable
    {
        $values = [
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

        foreach ($values as $value) {
            yield $value => [$value];
        }
    }

    /**
     * @dataProvider validColorProvider
     */
    public function test_validator_accepts_valid_color(string $value): void
    {
        $constraint = new HexColor;
        $this->validator->validate($value, $constraint);

        $this->assertNoViolation();
    }

    /**
     * @dataProvider invalidColorProvider
     */
    public function test_validator_rejects_invalid_color(string $value): void
    {
        $constraint = new HexColor;
        $this->validator->validate($value, $constraint);

        $this->buildViolation($constraint->message)
            ->setParameter('{{ value }}', $value)
            ->assertRaised()
        ;
    }
}
