<?php

namespace App\Export;

use App\Entity\RadioTable;
use App\Entity\RadioStation;
use Knp\Snappy\Pdf;

class PdfExporter implements ExporterInterface
{
    private $snappyPdfGenerator;
    private $htmlExporter;

    public function __construct(Pdf $snappyPdfGenerator, HtmlExporter $htmlExporter)
    {
        $this->snappyPdfGenerator = $snappyPdfGenerator;
        $this->htmlExporter = $htmlExporter;
    }

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
