<?php

namespace App\Util;

use App\Entity\Enum\RadioTable\Column;
use App\Entity\RadioTable;
use App\Entity\RadioStation;
use App\Util\RadioStationRdsTrait;
use App\Util\Data\RadioTableLabelsTrait;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use Symfony\Contracts\Translation\TranslatorInterface;
use Yectep\PhpSpreadsheetBundle\Factory;

class PhpSpreadsheetRenderer
{
    use RadioTableLabelsTrait;
    use RadioStationRdsTrait;

    public function __construct(private Factory $phpSpreadsheetFactory, private TranslatorInterface $translator) {}

    /**
     * @param RadioStation[] $radioStations
     */
    public function render(string $phpSpreadsheetWriterType, RadioTable $radioTable, array $radioStations): string
    {
        $spreadsheet = $this->getSpreadsheet($radioTable, $radioStations);

        $writer = $this->phpSpreadsheetFactory->createWriter(
            $spreadsheet,
            $phpSpreadsheetWriterType
        );

        ob_start();
        $writer->save('php://output');

        return ob_get_clean();
    }

    /**
     * @param RadioStation[] $radioStations
     */
    private function getSpreadsheet(RadioTable $radioTable, array $radioStations): Spreadsheet
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
            switch ($column) {
                case Column::FREQUENCY:
                    $value = $this->getFrequencyLabel($radioTable->getFrequencyUnit());
                    break;

                case Column::POWER:
                    $value = $this->getPowerLabel();
                    break;

                case Column::DISTANCE:
                    $value = $this->getDistanceLabel();
                    break;

                case Column::MAX_SIGNAL_LEVEL:
                    $value = $this->getMaxSignalLevelLabel($radioTable->getMaxSignalLevelUnit());
                    break;

                default:
                    $value = $this->translate('heading.' . $column->value);
            }

            $headings[] = $value;
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
                $value = $radioStation->{'get' . $column->value}();

                switch ($column) {
                    case Column::TYPE:
                    case Column::RECEPTION:
                        $value = $this->translate($column->value . '.' . $value->value);
                        break;

                    case Column::QUALITY:
                        $value = $this->getQualityLabel($value);
                        break;

                    case Column::POLARIZATION:
                        $value = $value ? $this->getPolarizationLabel($value) : '';
                        break;

                    case Column::DAB_CHANNEL:
                        $value = $value ? $value->value : '';
                        break;

                    case Column::COMMENT:
                        // Remove \r. It comes from <textarea> and breaks some apps like iWork Numbers in CSV format.
                        $value = str_replace("\r", '', $value);
                        break;

                    case Column::RDS:
                        $rds = $value;

                        $value = $rds->getPs()[0][0] ?? '';
                        if ($value) {
                            $value = str_replace(' ', '_', $this->alignRDSFrame($value));
                        }
                        break;
                }

                $row[] = $value;
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
