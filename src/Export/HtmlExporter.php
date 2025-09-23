<?php

namespace App\Export;

use App\Entity\RadioTable;
use Twig\Environment;

class HtmlExporter implements ExporterInterface
{
    public function __construct(private Environment $twig) {}

    public function render(string $format, RadioTable $radioTable, array $radioStations): string
    {
        return $this->twig->render('radio_table/standalone.html.twig', [
            'radio_table' => $radioTable,
            'radio_stations' => $radioStations,
        ]);
    }

    public function supports(string $format): bool
    {
        return 'html' === $format;
    }
}
