<?php

namespace App\Export;

use App\Entity\RadioTable;
use App\Util\SpreadsheetCreator;
use PhpOffice\PhpSpreadsheet\Writer\Csv;

class CsvExporter implements ExporterInterface
{
    public function __construct(private SpreadsheetCreator $spreadsheetCreator) {}

    /**
     * @param RadioStation[] $radioStations
     */
    public function render(RadioTable $radioTable, array $radioStations): string
    {
        $spreadsheet = $this->spreadsheetCreator->createSpreadsheet($radioTable, $radioStations);

        $writer = new Csv($spreadsheet);

        ob_start();
        $writer->save('php://output');

        return ob_get_clean();
    }
}
