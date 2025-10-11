<?php

namespace App\Twig;

use Twig\Attribute\AsTwigFilter;
use Twig\Environment;
use Twig\Extension\CoreExtension;
use Twig\Extra\Intl\IntlExtension;

class AppExtension
{
    #[AsTwigFilter('format_date_html', isSafe: ['html'])]
    public function formatDateHTML(Environment $twig, $date, ?string $dateFormat = 'long', ...$args): string
    {
        $intlExtension = $twig->getExtension(IntlExtension::class);
        $coreExtension = $twig->getExtension(CoreExtension::class);

        $date = $coreExtension->convertDate($date);

        return sprintf(
            '<time datetime="%s">%s</time>',
            $date->format('Y-m-d'),
            $intlExtension->formatDate($twig, $date, $dateFormat, ...$args)
        );
    }
}
