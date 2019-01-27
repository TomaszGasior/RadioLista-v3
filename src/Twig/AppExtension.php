<?php

namespace App\Twig;

use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class AppExtension extends AbstractExtension
{
    public function __construct(Environment $twig)
    {
        $twig
            ->getExtension('Twig_Extension_Core')
            ->setEscaper('csv', [$this, 'escapeCSV'])
        ;
    }

    public function getFilters(): array
    {
        return [
            new TwigFilter('date_format', [$this, 'formatDateAsTimeHTML'], [
                'needs_environment' => true, 'is_safe' => ['html']
            ]),
            new TwigFilter('format_rds_frames', [$this, 'formatRDSFrames']),
            new TwigFilter('strip_newlines', [$this, 'stripNewLineChars']),
        ];
    }

    public function formatDateAsTimeHTML(Environment $twig, $date): string
    {
        $date = twig_date_converter($twig, $date);

        $formatter = new \IntlDateFormatter(null, null, null);
        $formatter->setPattern('d MMMM Y');

        return '<time datetime="' . $date->format('Y-m-d') . '">' .
               $formatter->format($date->getTimestamp()) .
               '</time>';
    }

    public function formatRDSFrames(array $frames): array
    {
        foreach($frames as $key => &$frame) {
            $frame      = str_replace('_', ' ', $frame);
            $emptyChars = 8 - mb_strlen($frame);

            if ('' == trim($frame)) {
                unset($frames[$key]);
                continue;
            }

            if ($emptyChars > 0) {
                $frame = str_repeat(' ', floor($emptyChars/2)) . $frame . str_repeat(' ', ceil($emptyChars/2));
            }
            elseif ($emptyChars < 0) {
                $frame = htmlspecialchars(substr($frame, 0, 8));
            }
        }

        return $frames;
    }

    public function stripNewLineChars(?string $string): string
    {
        return preg_replace('/\R/u', '', $string);
    }

    public function escapeCSV(Environment $twig, $data): string
    {
        $handle = fopen('php://temp', 'w');

        fputcsv($handle, [$data]);
        rewind($handle);

        return substr(fgets($handle), 0, -1); // Trim ending newline char.
    }
}
