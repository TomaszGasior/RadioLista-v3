<?php

namespace App\Util;

use App\Entity\Enum\RadioTable\Column;
use App\Entity\RadioTable;
use App\Entity\RadioStation;
use App\Util\RadioStationRdsTrait;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use Symfony\Contracts\Translation\TranslatorInterface;

class SpreadsheetCreator
{
    use RadioStationRdsTrait;

    public function __construct(private TranslatorInterface $translator) {}

    /**
     * @param RadioStation[] $radioStations
     */
    public function createSpreadsheet(RadioTable $radioTable, array $radioStations): Spreadsheet
    {
        $spreadsheet = new Spreadsheet;
        $worksheet = $spreadsheet->getActiveSheet();

        $worksheet
            ->fromArray($this->getHeadings($radioTable))
            ->fromArray($this->getRows($radioTable, $radioStations), null, 'A2')
            ->setAutoFilter($worksheet->calculateWorksheetDimension())
            ->freezePane('A2')
        ;
        $worksheet
            ->getStyle('1')
            ->getFont()
            ->setBold(true)
        ;

        foreach (array_values($radioTable->getColumns()) as $i => $column) {
            $coordinate = Coordinate::stringFromColumnIndex($i + 1);

            $worksheet
                ->getColumnDimension($coordinate)
                ->setAutoSize(true)
            ;
            $worksheet
                ->getStyle($coordinate)
                ->getNumberFormat()
                ->setFormatCode($this->getColumnFormatting($column))
            ;
        }

        return $spreadsheet;
    }

    private function getHeadings(RadioTable $radioTable): array
    {
        $headings = [];

        foreach ($radioTable->getColumns() as $column) {
            $headings[] = match ($column) {
                Column::FREQUENCY => $radioTable->getFrequencyUnit()->getLabel(),
                Column::POWER => $radioTable->getPowerUnit()->getLabel(),
                Column::DISTANCE => $radioTable->getDistanceUnit()->getLabel(),
                Column::MAX_SIGNAL_LEVEL => $radioTable->getMaxSignalLevelUnit()->getLabel(),
                default => $this->translate('heading.' . $column->value),
            };
        }

        return $headings;
    }

    /**
     * @param RadioStation[] $radioStations
     */
    private function getRows(RadioTable $radioTable, array $radioStations): array
    {
        $data = [];

        foreach ($radioStations as $radioStation) {
            $row = [];

            foreach ($radioTable->getColumns() as $column) {
                $row[] = match ($column) {
                    Column::TYPE =>
                        $this->translate('type.' . $radioStation->getType()->value),
                    Column::RECEPTION =>
                        $this->translate('reception.' . $radioStation->getReception()->value),
                    Column::POLARIZATION =>
                        $radioStation->getPolarization()?->getLabel() ?: '',
                    Column::QUALITY =>
                        $radioStation->getQuality()->getLabel(),
                    Column::DAB_CHANNEL =>
                        $radioStation->getDabChannel()->value ?? '',
                    Column::COMMENT =>
                        // Remove \r. It comes from <textarea> and breaks some apps like iWork Numbers in CSV format.
                        str_replace("\r", '', (string) $radioStation->getComment()),
                    Column::RDS =>
                        str_replace(' ', '_', $this->alignRDSFrame($radioStation->getRds()->getPs()[0][0] ?? '')),
                    default =>
                        $radioStation->{'get' . $column->value}(),
                };
            }

            $data[] = $row;
        }

        return $data;
    }

    private function getColumnFormatting(Column $column): string
    {
        return match ($column) {
            Column::FREQUENCY => '0.000',
            Column::POWER => '0.000',
            Column::QUALITY => '0',
            Column::DISTANCE => '0',
            Column::MAX_SIGNAL_LEVEL => '0',
            Column::PRIVATE_NUMBER => '0',
            default => '@',
        };
    }

    private function translate(string $id): string
    {
        return $this->translator->trans($id, [], 'radio_table');
    }
}
