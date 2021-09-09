<?php

namespace App\Export;

use App\Entity\RadioTable;
use App\Entity\RadioStation;
use Twig\Environment;

class HtmlExporter implements ExporterInterface
{
    private $twig;

    public function __construct(Environment $twig)
    {
        $this->twig = $twig;
    }

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
