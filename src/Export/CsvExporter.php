<?php

namespace App\Export;

class CsvExporter extends AbstractPhpSpreadsheetExporter
{
    protected function getPhpSpreadsheetWriterType(): string
    {
        return 'Csv';
    }
}
