<?php

namespace App\Tests\Validator;

use App\Entity\Enum\RadioStation\DabChannel as DabChannelEnum;
use App\Entity\RadioStation;
use App\Entity\RadioTable;
use App\Entity\User;
use App\Validator\DabChannel;
use App\Validator\DabChannelValidator;
use App\Validator\HexColor;
use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Component\Validator\ConstraintValidatorInterface;
use Symfony\Component\Validator\Test\ConstraintValidatorTestCase;

class DabChannelValidatorTest extends ConstraintValidatorTestCase
{
    protected function createValidator(): ConstraintValidatorInterface
    {
        return new DabChannelValidator;
    }

    static public function validRadioStationProvider(): iterable
    {
        $frequencyToDabChannel = [
            '183.648' => DabChannelEnum::CHANNEL_6B,
            '216.928' => DabChannelEnum::CHANNEL_11A,
        ];

        foreach ($frequencyToDabChannel as $frequency => $dabChannel) {
            $radioStation = (new RadioStation('91.00', 'Name', new RadioTable('Name', new User('Name'))))
                ->setFrequency($frequency)
                ->setDabChannel($dabChannel)
            ;

            yield $dabChannel->value => [$radioStation];
        }
    }

    static public function invalidRadioStationProvider(): iterable
    {
        $frequencyToDabChannel = [
            '239.200' => DabChannelEnum::CHANNEL_6B,
            '174.928' => DabChannelEnum::CHANNEL_11A,
        ];

        foreach ($frequencyToDabChannel as $frequency => $dabChannel) {
            $radioStation = (new RadioStation('91.00', 'Name', new RadioTable('Name', new User('Name'))))
                ->setFrequency($frequency)
                ->setDabChannel($dabChannel)
            ;

            yield $dabChannel->value => [$radioStation];
        }
    }

    #[DataProvider('validRadioStationProvider')]
    public function test_validator_accepts_dab_channel_with_valid_frequency(RadioStation $radioStation): void
    {
        $this->setObject($radioStation);
        $value = $radioStation->getDabChannel();

        $constraint = new DabChannel;
        $this->validator->validate($value, $constraint);

        $this->assertNoViolation();
    }

    #[DataProvider('invalidRadioStationProvider')]
    public function test_validator_rejects_dab_channel_with_invalid_frequency(RadioStation $radioStation): void
    {
        $this->setObject($radioStation);
        $value = $radioStation->getDabChannel();

        $constraint = new DabChannel;
        $this->validator->validate($value, $constraint);

        $this->buildViolation($constraint->message)
            ->setParameter('{{ value }}', $value->value)
            ->assertRaised()
        ;
    }
}
