<?php

namespace App\Tests\Form\DataTransformer;

use App\Form\DataTransformer\RadioStationRdsRtFrameTransformer;
use PHPUnit\Framework\TestCase;

class RadioStationRdsRtFrameTransformerTest extends TestCase
{
    /**
     * @dataProvider dataProvider
     */
    public function testTransformFromDataToUi(array $data, string $transformedString): void
    {
        $transformer = new RadioStationRdsRtFrameTransformer;

        $this->assertEquals($transformedString, $transformer->transform($data));
    }

    /**
     * @dataProvider dataProvider
     * @dataProvider fromUiProvider
     */
    public function testTransformFromUiToData(array $data, string $transformedString): void
    {
        $transformer = new RadioStationRdsRtFrameTransformer;

        $this->assertEquals($data, $transformer->reverseTransform($transformedString));
    }

    public function dataProvider(): array
    {
        return [
            [
                [
                    ' 00-977 WARSZAWA    TE.022 645 9115    FAX.022 645 9515',
                    '*  POLSKIE RADIO SA  * PROGRAM 1  AL. NIEPODLEGLOSCI  77/85',
                ],
                ' 00-977 WARSZAWA    TE.022 645 9115    FAX.022 645 9515|*  POLSKIE RADIO SA  * PROGRAM 1  AL. NIEPODLEGLOSCI  77/85',
            ],
        ];
    }

    public function fromUiProvider(): array
    {
        return [
            [
                [
                    ' 00-977 WARSZAWA    TE.022 645 9115    FAX.022 645 9515',
                    '*  POLSKIE RADIO SA  * PROGRAM 1  AL. NIEPODLEGLOSCI  77/85',
                ],
                " 00-977 WARSZAWA    TE.022 645 9115    FAX.022 645 9515\r\n*  POLSKIE RADIO SA  * PROGRAM 1  AL. NIEPODLEGLOSCI  77/85",
            ],

            [
                [
                    'RADIO KATOWICE  102,2 FM  103,0 FM  98,4 FM  89,3 FM  97,0 FM',
                    'WIDZISZ WYPADEK LUB ZAGROZENIE NA DRODZE... ZADZWON LUB WYSLIJ',
                    'SMS - AUTO RADIO 691 400 400  ANTENA - 32 205 102 2',
                    'WWW.RADIO.KATOWICE.PL',
                ],
                "RADIO KATOWICE  102,2 FM  103,0 FM  98,4 FM  89,3 FM  97,0 FM\nWIDZISZ WYPADEK LUB ZAGROZENIE NA DRODZE... ZADZWON LUB WYSLIJ|SMS - AUTO RADIO 691 400 400  ANTENA - 32 205 102 2\r\nWWW.RADIO.KATOWICE.PL",
            ],

            [
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
}
