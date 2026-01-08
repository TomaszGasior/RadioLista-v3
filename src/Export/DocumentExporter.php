<?php

namespace App\Export;

use App\Entity\RadioTable;
use App\Enum\ExportFormat;
use App\Util\DompdfFactory;
use Twig\Environment;

class DocumentExporter implements ExporterInterface
{
    public const int PDF_FORMAT_RADIO_STATIONS_MAX_COUNT = 500;

    public function __construct(
        private Environment $twig,
        private DompdfFactory $dompdfFactory,
    ) {}

    public function render(ExportFormat $format, RadioTable $radioTable, array $radioStations): string
    {
        if ($format === ExportFormat::PDF) {
            // Dompdf's performance is very bad when rendering documents containing long tables.
            // Limit the number of rows to avoid exceeding PHP's memory limit or execution timeout.
            $radioStations = array_slice($radioStations, 0, self::PDF_FORMAT_RADIO_STATIONS_MAX_COUNT);
        }

        $html = $this->twig->render('radio_table/standalone.html.twig', [
            'radio_table' => $radioTable,
            'radio_stations' => $radioStations,
        ]);

        if ($format === ExportFormat::PDF) {
            $dompdf = $this->dompdfFactory->getDompdf();

            $dompdf->getOptions()->setDpi(110);
            $dompdf->setPaper('A4', 'landscape');
            $dompdf->loadHtml($html);
            $dompdf->render();

            return $dompdf->output();
        }

        return $html;
    }

    public function supports(ExportFormat $format): bool
    {
        return in_array($format, [ExportFormat::HTML, ExportFormat::PDF]);
    }
}
