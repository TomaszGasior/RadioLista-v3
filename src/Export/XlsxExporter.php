<?php

namespace App\Export;

class XlsxExporter extends AbstractPhpSpreadsheetExporter
{
    protected function getPhpSpreadsheetWriterType(): string
    {
        return 'Xlsx';
    }
}
