<?php

namespace App\Twig;

use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\Extension\EscaperExtension;
use Twig\Extra\Intl\IntlExtension;
use Twig\TwigFilter;

class AppExtension extends AbstractExtension
{
    public function __construct(Environment $twig)
    {
        /** @var EscaperExtension */
        $escaperExtension = $twig->getExtension(EscaperExtension::class);
        $escaperExtension->setEscaper('csv', [$this, 'escapeCSV']);
    }

    /**
     * @codeCoverageIgnore
     */
    public function getFilters(): array
    {
        return [
            new TwigFilter('format_date_html', [$this, 'formatDateHTML'], [
                'needs_environment' => true, 'is_safe' => ['html']
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
