<?php

namespace App\Export;

use App\Entity\RadioTable;
use App\Util\PhpSpreadsheetRenderer;

class OdsExporter implements ExporterInterface
{
    private $phpSpreadsheetRenderer;

    public function __construct(PhpSpreadsheetRenderer $phpSpreadsheetRenderer)
    {
        $this->phpSpreadsheetRenderer = $phpSpreadsheetRenderer;
    }

    /**
     * @param RadioStation[] $radioStations
     */
    public function render(RadioTable $radioTable, array $radioStations): string
    {
        return $this->phpSpreadsheetRenderer->render('Ods', $radioTable, $radioStations);
    }
}
