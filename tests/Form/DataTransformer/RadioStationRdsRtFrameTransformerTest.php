<?php

namespace App\Tests\Form\DataTransformer;

use App\Form\DataTransformer\RadioStationRdsRtFrameTransformer;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class RadioStationRdsRtFrameTransformerTest extends TestCase
{
    static public function dataProvider(): array
    {
        return [
            'standard' => [
                [
                    ' 00-977 WARSZAWA    TE.022 645 9115    FAX.022 645 9515',
                    '*  POLSKIE RADIO SA  * PROGRAM 1  AL. NIEPODLEGLOSCI  77/85',
                ],
                " 00-977 WARSZAWA    TE.022 645 9115    FAX.022 645 9515\n*  POLSKIE RADIO SA  * PROGRAM 1  AL. NIEPODLEGLOSCI  77/85",
            ],
        ];
    }

    static public function dataWithExtraWhitespaceProvider(): array
    {
        return [
            'Windows newline char' => [
                [
                    ' 00-977 WARSZAWA    TE.022 645 9115    FAX.022 645 9515',
                    '*  POLSKIE RADIO SA  * PROGRAM 1  AL. NIEPODLEGLOSCI  77/85',
                ],
                " 00-977 WARSZAWA    TE.022 645 9115    FAX.022 645 9515\r\n*  POLSKIE RADIO SA  * PROGRAM 1  AL. NIEPODLEGLOSCI  77/85",
            ],

            'inconsistent newline char' => [
                [
                    'RADIO KATOWICE  102,2 FM  103,0 FM  98,4 FM  89,3 FM  97,0 FM',
                    'WIDZISZ WYPADEK LUB ZAGROZENIE NA DRODZE... ZADZWON LUB WYSLIJ',
                    'SMS - AUTO RADIO 691 400 400  ANTENA - 32 205 102 2',
                    'WWW.RADIO.KATOWICE.PL',
                ],
                "RADIO KATOWICE  102,2 FM  103,0 FM  98,4 FM  89,3 FM  97,0 FM\nWIDZISZ WYPADEK LUB ZAGROZENIE NA DRODZE... ZADZWON LUB WYSLIJ|SMS - AUTO RADIO 691 400 400  ANTENA - 32 205 102 2\r\nWWW.RADIO.KATOWICE.PL",
            ],

            'empty input lines' => [
                [
                    'RADIO KATOWICE  102,2 FM  103,0 FM  98,4 FM  89,3 FM  97,0 FM',
                    'WIDZISZ WYPADEK LUB ZAGROZENIE NA DRODZE... ZADZWON LUB WYSLIJ',
                    'SMS - AUTO RADIO 691 400 400  ANTENA - 32 205 102 2',
                    'WWW.RADIO.KATOWICE.PL',
                ],
                "RADIO KATOWICE  102,2 FM  103,0 FM  98,4 FM  89,3 FM  97,0 FM\n\n\nWIDZISZ WYPADEK LUB ZAGROZENIE NA DRODZE... ZADZWON LUB WYSLIJ||||||SMS - AUTO RADIO 691 400 400  ANTENA - 32 205 102 2\nWWW.RADIO.KATOWICE.PL",
            ],
        ];
    }

    #[DataProvider('dataProvider')]
    public function test_transforms_from_internal_structure_to_user_input(array $data, string $transformedString): void
    {
        $transformer = new RadioStationRdsRtFrameTransformer;

        $this->assertEquals($transformedString, $transformer->transform($data));
    }

    #[DataProvider('dataProvider')]
    #[DataProvider('dataWithExtraWhitespaceProvider')]
    public function test_transforms_from_user_input_to_internal_structure(array $data, string $transformedString): void
    {
        $transformer = new RadioStationRdsRtFrameTransformer;

        $this->assertEquals($data, $transformer->reverseTransform($transformedString));
    }
}
