<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class AppExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            new TwigFilter('date_format', [$this, 'formatDateAsTimeHTML'], [
                'needs_environment' => true, 'is_safe' => ['html']
            ]),
        ];
    }

    public function formatDateAsTimeHTML(\Twig_Environment $twig, $date): string
    {
        $date = twig_date_converter($twig, $date);

        $formatter = new \IntlDateFormatter(null, null, null);
        $formatter->setPattern('d MMMM Y');

        return '<time datetime="' . $date->format('Y-m-d') . '">' .
               $formatter->format($date->getTimestamp()) .
               '</time>';
    }
}
