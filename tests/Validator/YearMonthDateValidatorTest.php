<?php

namespace App\Tests\Validator;

use App\Validator\YearMonthDate;
use App\Validator\YearMonthDateValidator;
use Symfony\Component\Validator\ConstraintValidatorInterface;
use Symfony\Component\Validator\Test\ConstraintValidatorTestCase;

class YearMonthDateValidatorTest extends ConstraintValidatorTestCase
{
    protected function createValidator(): ConstraintValidatorInterface
    {
        return new YearMonthDateValidator;
    }

    /**
     * @dataProvider dataProvider
     */
    public function testYearMonthDateValidator(string $value, bool $isValid): void
    {
        $constraint = new YearMonthDate;
        $this->validator->validate($value, $constraint);

        if ($isValid) {
            $this->assertNoViolation();
        }
        else {
            $this->buildViolation($constraint->message)
                ->setParameter('{{ value }}', '"'.$value.'"')
                ->setCode(YearMonthDate::INVALID_FORMAT_ERROR)
                ->assertRaised();
        }
    }

    public function dataProvider(): iterable
    {
        $validValues = [
            '1991',
            '2003-01',
            '2016-02-29',
        ];
        $invalidValues = [
            '2015-13',
            '201513',
            // Other cases like incorrect day of month are covered
            // in base DateValidator class.
        ];

        foreach ($validValues as $value) {
            yield [$value, true];
        }
        foreach ($invalidValues as $value) {
            yield [$value, false];
        }
    }
}
