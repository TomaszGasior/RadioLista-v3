<?php

namespace App\Export;

use App\Entity\RadioTable;
use App\Entity\RadioStation;
use Knp\Snappy\Pdf;

class PdfExporter implements ExporterInterface
{
    public function __construct(private Pdf $snappyPdfGenerator, private HtmlExporter $htmlExporter) {}

    /**
     * @param RadioStation[] $radioStations
     */
    public function render(RadioTable $radioTable, array $radioStations): string
    {
        $html = $this->htmlExporter->render($radioTable, $radioStations);

        return $this->snappyPdfGenerator->getOutputFromHtml($html, [
            'orientation' => 'Landscape',
            'margin-left' => 0,
            'margin-top' => 9,
            'margin-bottom' => 8,
            'margin-right' => 0,
            'zoom' => 1.15,
        ]);
    }
}
