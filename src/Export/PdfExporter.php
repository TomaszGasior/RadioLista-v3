<?php

namespace App\Export;

use App\Entity\RadioTable;
use App\Entity\RadioStation;
use App\Util\PdfMetadataPatcher;
use Knp\Snappy\Pdf;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

class PdfExporter implements ExporterInterface
{
    public function __construct(
        private HtmlExporter $htmlExporter,
        private Pdf $snappyPdfGenerator,
        private PdfMetadataPatcher $pdfMetadataPatcher,
        #[Autowire('%app.version%')] private string $version,
    ) {}

    /**
     * @param RadioStation[] $radioStations
     */
    public function render(RadioTable $radioTable, array $radioStations): string
    {
        $html = $this->htmlExporter->render($radioTable, $radioStations);

        $pdf = $this->snappyPdfGenerator->getOutputFromHtml($html, [
            'orientation' => 'Landscape',
            'margin-left' => 0,
            'margin-top' => 9,
            'margin-bottom' => 8,
            'margin-right' => 0,
            'zoom' => 1.15,
        ]);

        return $this->pdfMetadataPatcher->replaceCreatorMetadata($pdf, 'RadioLista ' . $this->version);
    }
}
