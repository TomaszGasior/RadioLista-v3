<?php

namespace App\Export;

class OdsExporter extends AbstractPhpSpreadsheetExporter
{
    protected function getPhpSpreadsheetWriterType(): string
    {
        return 'Ods';
    }
}
