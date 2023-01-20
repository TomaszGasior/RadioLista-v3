<?php

namespace App\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

/**
 * The following transformer is designed for RadioStation::$rds.ps array.
 * It's used by RadioStationEditType form field.
 *
 * RadioStation::$rds.ps contains array with nested arrays with strings.
 * In transformed string nested arrays need to be separated by newline
 * and string of each frame need to be separated by "|".
 */
class RadioStationRdsPsFrameTransformer implements DataTransformerInterface
{
    public function transform(mixed $value): ?string
    {
        if (false === is_array($value)) {
            return null;
        }

        $lines = [];

        foreach ($value as $item) {
            if (false === is_array($item)) {
                throw new TransformationFailedException;
            }

            $lines[] = implode('|', $item);
        }

        return implode("\n", $lines);
    }

    public function reverseTransform(mixed $value): array
    {
        // Normalize whitespace chars.
        $value = str_replace(["\r", "\t"], '', $value);

        // Avoid zero-length frames at the beginning or at the end of line.
        $value = str_replace(["\n|", "|\n", "|\n|"], "\n", $value);

        $result = [];
        $lines = explode("\n", $value);

        foreach ($lines as $item) {
            if ('' === $item) {
                continue;
            }

            $frames = explode('|', $item);
            $frames = array_values(array_filter($frames, function($item){
                return '' !== $item;
            }));

            $result[] = $frames;
        }

        return $result;
    }
}
