<?php

namespace App\Export;

use App\Entity\RadioTable;
use App\Enum\ExportFormat;
use Twig\Environment;

class HtmlExporter implements ExporterInterface
{
    public function __construct(private Environment $twig) {}

    public function render(ExportFormat $format, RadioTable $radioTable, array $radioStations): string
    {
        return $this->twig->render('radio_table/standalone.html.twig', [
            'radio_table' => $radioTable,
            'radio_stations' => $radioStations,
        ]);
    }

    public function supports(ExportFormat $format): bool
    {
        return ExportFormat::HTML === $format;
    }
}
