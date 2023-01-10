<?php

namespace App\Tests\Form\DataTransformer;

use App\Form\DataTransformer\RadioStationRdsPsFrameTransformer;
use PHPUnit\Framework\TestCase;

class RadioStationRdsPsFrameTransformerTest extends TestCase
{
    public function dataProvider(): array
    {
        return [
            'one line' => [
                [
                    ['POLSKIE', ' RADIO', 'JEDYNKA ', ' 16:09'],
                ],
                'POLSKIE| RADIO|JEDYNKA | 16:09',
            ],

            'multiline' => [
                [
                    ['RMF FM', 'KATOWICE', '93,0 MHZ'],
                    ['RMF FM', 'NAJ-', '-LEPSZA', 'MUZYKA'],
                    ['RMF FM', 'PERFECT'],
                ],
                "RMF FM|KATOWICE|93,0 MHZ\nRMF FM|NAJ-|-LEPSZA|MUZYKA\nRMF FM|PERFECT",
            ],
        ];
    }

    public function dataWithExtraWhitespaceProvider(): array
    {
        return [
            'Windows newline char' => [
                [
                    ['RMF FM', 'KATOWICE', '93,0 MHZ'],
                    ['RMF FM', 'NAJ-', '-LEPSZA', 'MUZYKA'],
                    ['RMF FM', 'PERFECT'],
                ],
                "RMF FM|KATOWICE|93,0 MHZ|\r\nRMF FM|NAJ-|-LEPSZA|MUZYKA|\nRMF FM|PERFECT",
            ],

            'empty input lines' => [
                [
                    ['RMF FM', 'KATOWICE', '93,0 MHZ'],
                    ['RMF FM', 'NAJ-', '-LEPSZA', 'MUZYKA'],
                    ['RMF FM', 'PERFECT'],
                ],
                "RMF FM|KATOWICE|93,0 MHZ|\t\n|RMF FM|NAJ-|-LEPSZA||||MUZYKA\n\n\r\n\nRMF FM|PERFECT",
            ],
        ];
    }

    /**
     * @dataProvider dataProvider
     */
    public function test_transforms_from_internal_structure_to_user_input(array $data, string $transformedString): void
    {
        $transformer = new RadioStationRdsPsFrameTransformer;

        $this->assertEquals($transformedString, $transformer->transform($data));
    }

    /**
     * @dataProvider dataProvider
     * @dataProvider dataWithExtraWhitespaceProvider
     */
    public function test_transforms_from_user_input_to_internal_structure(array $data, string $transformedString): void
    {
        $transformer = new RadioStationRdsPsFrameTransformer;

        $this->assertEquals($data, $transformer->reverseTransform($transformedString));
    }
}
