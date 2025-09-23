<?php

namespace App\Export;

use App\Entity\RadioTable;
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

    public function render(string $format, RadioTable $radioTable, array $radioStations): string
    {
        $html = $this->htmlExporter->render('html', $radioTable, $radioStations);

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

    public function supports(string $format): bool
    {
        return 'pdf' === $format;
    }
}
