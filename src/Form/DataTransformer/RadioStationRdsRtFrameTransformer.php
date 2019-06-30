<?php

namespace App\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;

// The following transformer is designed for RadioStation::$rds[rt] array.
// It's used by RadioStationEditType form field.
//
// RadioStation::$rds[rt] contains array with strings which need to be
// separated by "|" character in transformed string.

class RadioStationRdsRtFrameTransformer implements DataTransformerInterface
{
    public function transform($value): ?string
    {
        if (false === is_array($value)) {
            return null;
        }

        return implode("\n", $value);
    }

    public function reverseTransform($value): array
    {
        // Normalize whitespace chars.
        $value = str_replace(["\r", "\t"], '', $value);

        // Treat both chars as separators.
        $value = str_replace("\n", '|', $value);

        $frames = explode('|', $value);
        $frames = array_values(array_filter($frames, function($item){
            return '' !== $item;
        }));

        return $frames;
    }
}
