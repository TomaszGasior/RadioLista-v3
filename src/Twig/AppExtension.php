<?php

namespace App\Twig;

use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\Extension\CoreExtension;
use Twig\Extension\EscaperExtension;
use Twig\TwigFilter;

class AppExtension extends AbstractExtension
{
    public function __construct(Environment $twig, Compiler $compiler)
    {
        $twig
            ->getExtension(EscaperExtension::class)
            ->setEscaper('csv', [$this, 'escapeCSV'])
        ;

        // Set custom compiler to minify HTML output of cached Twig templates.
        $twig->setCompiler($compiler);
    }

    public function getFilters(): array
    {
        return [
            new TwigFilter('date_format', [$this, 'formatDateAsTimeHTML'], [
                'needs_environment' => true, 'is_safe' => ['html']
            ]),
            new TwigFilter('soft_number_format', [$this, 'softNumberFormat'], [
                'needs_environment' => true
            ]),
            new TwigFilter('align_rds_frame', [$this, 'alignRDSFrame']),
        ];
    }

    public function formatDateAsTimeHTML(Environment $twig, $date): string
    {
        $date = twig_date_converter($twig, $date);

        $formatter = new \IntlDateFormatter(null, null, null);
        $formatter->setPattern('d MMMM y');

        return sprintf(
            '<time datetime="%s">%s</time>',
            $date->format('Y-m-d'),
            $formatter->format($date->getTimestamp())
        );
    }

    public function softNumberFormat(Environment $twig, $number, int $decimal = null,
                                     string $decimalPoint = null, string $thousandSep = null): string
    {
        if (null === $decimal) {
            $decimal = $twig->getExtension(CoreExtension::class)->getNumberFormat()[0];
        }

        // Don't round values with precision bigger than preferred.
        $sourceDecimal = strlen(strstr((float)(string)$number, '.')) - 1;
        if ($sourceDecimal > $decimal) {
            $decimal = $sourceDecimal;
        }

        return twig_number_format_filter($twig, $number, $decimal, $decimalPoint, $thousandSep);
    }

    public function alignRDSFrame(string $frame): string
    {
        $emptyChars = 8 - mb_strlen($frame);

        if ($emptyChars > 0) {
            $frame = str_repeat(' ', floor($emptyChars/2)) . $frame . str_repeat(' ', ceil($emptyChars/2));
        }
        elseif ($emptyChars < 0) {
            $frame = substr($frame, 0, 8);
        }

        return $frame;
    }

    public function escapeCSV(Environment $twig, $data): string
    {
        $handle = fopen('php://temp', 'w');

        fputcsv($handle, [$data]);
        rewind($handle);

        $text = '';

        while (false === feof($handle)) {
            $text .= fgets($handle);
        }

        fclose($handle);

        return substr($text, 0, -1); // Trim ending newline char.
    }
}
