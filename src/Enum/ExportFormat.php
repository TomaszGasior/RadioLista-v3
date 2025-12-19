<?php

namespace App\Enum;

enum ExportFormat: string
{
    case CSV = 'csv';
    case ODS = 'ods';
    case XLSX = 'xlsx';
    case HTML = 'html';
    case PDF = 'pdf';
}
