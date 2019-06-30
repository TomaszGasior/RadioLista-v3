<?php

namespace App\Tests\Form\DataTransformer;

use App\Form\DataTransformer\RadioStationRdsPsFrameTransformer;
use PHPUnit\Framework\TestCase;

class RadioStationRdsPsFrameTransformerTest extends TestCase
{
    /**
     * @dataProvider dataProvider
     */
    public function testTransformFromDataToUi(array $data, string $transformedString): void
    {
        $transformer = new RadioStationRdsPsFrameTransformer;

        $this->assertEquals($transformedString, $transformer->transform($data));
    }

    /**
     * @dataProvider dataProvider
     * @dataProvider fromUiProvider
     */
    public function testTransformFromUiToData(array $data, string $transformedString): void
    {
        $transformer = new RadioStationRdsPsFrameTransformer;

        $this->assertEquals($data, $transformer->reverseTransform($transformedString));
    }

    public function dataProvider(): array
    {
        return [
            [
                [
                    ['POLSKIE', ' RADIO', 'JEDYNKA ', ' 16:09'],
                ],
                'POLSKIE| RADIO|JEDYNKA | 16:09',
            ],

            [
                [
                    ['RMF FM', 'KATOWICE', '93,0 MHZ'],
                    ['RMF FM', 'NAJ-', '-LEPSZA', 'MUZYKA'],
                    ['RMF FM', 'PERFECT'],
                ],
                "RMF FM|KATOWICE|93,0 MHZ\nRMF FM|NAJ-|-LEPSZA|MUZYKA\nRMF FM|PERFECT",
            ],
        ];
    }

    public function fromUiProvider(): array
    {
        return [
            [
                [
                    ['RMF FM', 'KATOWICE', '93,0 MHZ'],
                    ['RMF FM', 'NAJ-', '-LEPSZA', 'MUZYKA'],
                    ['RMF FM', 'PERFECT'],
                ],
                "RMF FM|KATOWICE|93,0 MHZ|\r\nRMF FM|NAJ-|-LEPSZA|MUZYKA|\nRMF FM|PERFECT",
            ],

            [
                [
                    ['RMF FM', 'KATOWICE', '93,0 MHZ'],
                    ['RMF FM', 'NAJ-', '-LEPSZA', 'MUZYKA'],
                    ['RMF FM', 'PERFECT'],
                ],
                "RMF FM|KATOWICE|93,0 MHZ|\t\n|RMF FM|NAJ-|-LEPSZA||||MUZYKA\n\n\r\n\nRMF FM|PERFECT",
            ],
        ];
    }
}
