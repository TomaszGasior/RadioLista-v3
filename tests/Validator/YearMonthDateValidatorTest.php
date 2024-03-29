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

    public function validDateProvider(): iterable
    {
        $values = [
            '1991',
            '2003-01',
            '2016-02-29',
        ];

        foreach ($values as $value) {
            yield '"'.$value.'"' => [$value];
        }
    }

    public function invalidDateProvider(): iterable
    {
        $valuesWithErrorCode = [
            ['2015-13', YearMonthDate::INVALID_FORMAT_ERROR],
            ['201513', YearMonthDate::INVALID_FORMAT_ERROR],
            ['2001-02-30', YearMonthDate::INVALID_DATE_ERROR],
        ];

        foreach ($valuesWithErrorCode as [$value, $errorCode]) {
            yield '"'.$value.'"' => [$value, $errorCode];
        }
    }

    /**
     * @dataProvider validDateProvider
     */
    public function test_validator_accepts_correct_date(string $value): void
    {
        $constraint = new YearMonthDate;
        $this->validator->validate($value, $constraint);

        $this->assertNoViolation();
    }

    /**
     * @dataProvider invalidDateProvider
     */
    public function test_validator_rejects_incorrect_date(string $value, string $errorCode): void
    {
        $constraint = new YearMonthDate;
        $this->validator->validate($value, $constraint);

        $this->buildViolation($constraint->message)
            ->setParameter('{{ value }}', '"'.$value.'"')
            ->setCode($errorCode)
            ->assertRaised()
        ;
    }
}
