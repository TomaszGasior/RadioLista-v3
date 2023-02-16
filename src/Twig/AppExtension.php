<?php

namespace App\Twig;

use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\Extra\Intl\IntlExtension;
use Twig\TwigFilter;

class AppExtension extends AbstractExtension
{
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
        $intlExtension = $twig->getExtension(IntlExtension::class);
        $date = twig_date_converter($twig, $date);

        return sprintf(
            '<time datetime="%s">%s</time>',
            $date->format('Y-m-d'),
            $intlExtension->formatDate($twig, $date, $dateFormat, ...$args)
        );
    }
}
