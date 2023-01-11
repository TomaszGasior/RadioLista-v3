<?php

namespace App\Tests\Validator;

use App\Entity\RadioStation;
use App\Validator\DabChannel;
use App\Validator\DabChannelValidator;
use App\Validator\HexColor;
use Symfony\Component\Validator\ConstraintValidatorInterface;
use Symfony\Component\Validator\Test\ConstraintValidatorTestCase;

class DabChannelValidatorTest extends ConstraintValidatorTestCase
{
    protected function createValidator(): ConstraintValidatorInterface
    {
        return new DabChannelValidator;
    }

    public function validProvider(): iterable
    {
        $dabChannelToFrequency = [
            '6B' => '183.648',
            '11A' => '216.928',
        ];

        foreach ($dabChannelToFrequency as $dabChannel => $frequency) {
            $radioStation = (new RadioStation)
                ->setFrequency($frequency)
                ->setDabChannel($dabChannel)
            ;

            yield $dabChannel => [$radioStation];
        }
    }

    public function invalidFrequencyProvider(): iterable
    {
        $dabChannelToFrequency = [
            '6B' => '239.200',
            '11A' => '174.928',
        ];

        foreach ($dabChannelToFrequency as $dabChannel => $frequency) {
            $radioStation = (new RadioStation)
                ->setFrequency($frequency)
                ->setDabChannel($dabChannel)
            ;

            yield $dabChannel => [$radioStation];
        }
    }

    public function unknownDabChannelProvider(): iterable
    {
        $dabChannelToFrequency = [
            '99X' => '183.648',
            'NNN' => '216.928',
        ];

        foreach ($dabChannelToFrequency as $dabChannel => $frequency) {
            $radioStation = (new RadioStation)
                ->setFrequency($frequency)
                ->setDabChannel($dabChannel)
            ;

            yield $dabChannel => [$radioStation];
        }
    }

    /**
     * @dataProvider validProvider
     */
    public function test_validator_accepts_dab_channel_with_valid_frequency(RadioStation $radioStation): void
    {
        $this->setObject($radioStation);
        $value = $radioStation->getDabChannel();

        $constraint = new DabChannel;
        $this->validator->validate($value, $constraint);

        $this->assertNoViolation();
    }

    /**
     * @dataProvider invalidFrequencyProvider
     */
    public function test_validator_rejects_dab_channel_with_invalid_frequency(RadioStation $radioStation): void
    {
        $this->setObject($radioStation);
        $value = $radioStation->getDabChannel();

        $constraint = new DabChannel;
        $this->validator->validate($value, $constraint);

        $this->buildViolation($constraint->message)
            ->setParameter('{{ value }}', $value)
            ->assertRaised()
        ;
    }

    /**
     * @dataProvider unknownDabChannelProvider
     */
    public function test_validator_rejects_unknown_dab_channel(RadioStation $radioStation): void
    {
        $this->setObject($radioStation);
        $value = $radioStation->getDabChannel();

        $constraint = new DabChannel;
        $this->validator->validate($value, $constraint);

        $this->buildViolation($constraint->message)
            ->setParameter('{{ value }}', $value)
            ->assertRaised()
        ;
    }
}
