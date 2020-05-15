<?php

namespace App\Twig;

use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\Extension\CoreExtension;
use Twig\Extension\EscaperExtension;
use Twig\Extra\Intl\IntlExtension;
use Twig\TwigFilter;

class AppExtension extends AbstractExtension
{
    public function __construct(Environment $twig, Compiler $compiler)
    {
        /** @var EscaperExtension */
        $escaperExtension = $twig->getExtension(EscaperExtension::class);
        $escaperExtension->setEscaper('csv', [$this, 'escapeCSV']);

        // Set custom compiler to minify HTML output of cached Twig templates.
        $twig->setCompiler($compiler);
    }

    public function getFilters(): array
    {
        return [
            new TwigFilter('format_date_html', [$this, 'formatDateHTML'], [
                'needs_environment' => true, 'is_safe' => ['html']
            ]),
            new TwigFilter('soft_number_format', [$this, 'softNumberFormat'], [
                'needs_environment' => true
            ]),
        ];
    }

    public function formatDateHTML(Environment $twig, $date, ?string $dateFormat = 'long', ...$args): string
    {
        /** @var IntlExtension */
        $intlExtension = $twig->getExtension(IntlExtension::class);

        $date = twig_date_converter($twig, $date);

        return sprintf(
            '<time datetime="%s">%s</time>',
            $date->format('Y-m-d'),
            $intlExtension->formatDate($twig, $date, $dateFormat, ...$args)
        );
    }

    public function softNumberFormat(Environment $twig, $number, int $decimal = null,
                                     string $decimalPoint = null, string $thousandSep = null): string
    {
        if (null === $decimal) {
            /** @var CoreExtension */
            $coreExtension = $twig->getExtension(CoreExtension::class);
            $decimal = $coreExtension->getNumberFormat()[0];
        }

        // Don't round values with precision bigger than preferred.
        $sourceDecimal = strlen(strstr((float)(string)$number, '.')) - 1;
        if ($sourceDecimal > $decimal) {
            $decimal = $sourceDecimal;
        }

        return twig_number_format_filter($twig, $number, $decimal, $decimalPoint, $thousandSep);
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
