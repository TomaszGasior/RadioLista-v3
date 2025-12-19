<?php

namespace App\Export;

use App\Entity\RadioTable;
use App\Enum\ExportFormat;
use App\Util\DompdfFactory;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

class PdfExporter implements ExporterInterface
{
    public const int RADIO_STATIONS_MAX_COUNT = 500;

    public function __construct(
        private HtmlExporter $htmlExporter,
        private DompdfFactory $dompdfFactory,
        #[Autowire('%app.version%')] private string $version,
    ) {}

    public function render(ExportFormat $format, RadioTable $radioTable, array $radioStations): string
    {
        // Dompdf's performance is very bad when rendering documents containing long tables.
        // Limit the number of rows to avoid exceeding PHP's memory limit or execution timeout.
        $radioStations = array_slice($radioStations, 0, self::RADIO_STATIONS_MAX_COUNT);

        $html = $this->htmlExporter->render(ExportFormat::HTML, $radioTable, $radioStations);

        $dompdf = $this->dompdfFactory->getDompdf();

        $dompdf->getOptions()->setDpi(110);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->addInfo('Creator', 'RadioLista ' . $this->version);

        $dompdf->loadHtml($html);
        $dompdf->render();

        return $dompdf->output();
    }

    public function supports(ExportFormat $format): bool
    {
        return ExportFormat::PDF === $format;
    }
}
