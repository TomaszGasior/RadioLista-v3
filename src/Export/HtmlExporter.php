<?php

namespace App\Export;

use App\Entity\RadioTable;
use App\Entity\RadioStation;
use Twig\Environment;

class HtmlExporter implements ExporterInterface
{
    public function __construct(private Environment $twig) {}

    /**
     * @param RadioStation[] $radioStations
     */
    public function render(RadioTable $radioTable, array $radioStations): string
    {
        return $this->twig->render('radio_table/standalone.html.twig', [
            'radio_table' => $radioTable,
            'radio_stations' => $radioStations,
        ]);
    }
}
