<?php

namespace App\Export;

use App\Entity\RadioTable;
use App\Enum\ExportFormat;
use App\Util\SpreadsheetCreator;
use PhpOffice\PhpSpreadsheet\Writer\Csv;
use PhpOffice\PhpSpreadsheet\Writer\Ods;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class SpreadsheetExporter implements ExporterInterface
{
    public function __construct(private SpreadsheetCreator $spreadsheetCreator) {}

    public function render(ExportFormat $format, RadioTable $radioTable, array $radioStations): string
    {
        $spreadsheet = $this->spreadsheetCreator->createSpreadsheet($radioTable, $radioStations);

        $writer = match ($format) {
            ExportFormat::CSV => new Csv($spreadsheet),
            ExportFormat::ODS => new Ods($spreadsheet),
            ExportFormat::XLSX => new Xlsx($spreadsheet),
        };

        ob_start();
        $writer->save('php://output');

        return ob_get_clean();
    }

    public function supports(ExportFormat $format): bool
    {
        return in_array($format, [ExportFormat::CSV, ExportFormat::ODS, ExportFormat::XLSX]);
    }
}
