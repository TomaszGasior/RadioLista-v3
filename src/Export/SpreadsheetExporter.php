<?php

namespace App\Export;

use App\Entity\RadioTable;
use App\Util\SpreadsheetCreator;
use PhpOffice\PhpSpreadsheet\Writer\Csv;
use PhpOffice\PhpSpreadsheet\Writer\Ods;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class SpreadsheetExporter implements ExporterInterface
{
    private const array WRITERS = [
        'ods' => Ods::class,
        'xlsx' => Xlsx::class,
        'csv' => Csv::class,
    ];

    public function __construct(private SpreadsheetCreator $spreadsheetCreator) {}

    public function render(string $format, RadioTable $radioTable, array $radioStations): string
    {
        $spreadsheet = $this->spreadsheetCreator->createSpreadsheet($radioTable, $radioStations);

        $writer = new (self::WRITERS[$format])($spreadsheet);

        ob_start();
        $writer->save('php://output');

        return ob_get_clean();
    }

    public function supports(string $format): bool
    {
        return in_array($format, array_keys(self::WRITERS));
    }
}
