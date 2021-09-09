<?php

namespace App\Export;

use App\Entity\Embeddable\RadioStation\Locality;
use App\Entity\RadioTable;
use App\Entity\RadioStation;
use App\Util\RadioStationRdsTrait;
use App\Util\RadioTableLabelsTrait;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use Symfony\Contracts\Translation\TranslatorInterface;
use Yectep\PhpSpreadsheetBundle\Factory;

abstract class AbstractPhpSpreadsheetExporter implements ExporterInterface
{
    use RadioTableLabelsTrait;
    use RadioStationRdsTrait;

    private const COLUMN_NUMBER_FORMATTING = [
        RadioTable::COLUMN_FREQUENCY => '0.000',
        RadioTable::COLUMN_POWER => '0.000',
        RadioTable::COLUMN_QUALITY => '0',
        RadioTable::COLUMN_DISTANCE => '0',
        RadioTable::COLUMN_MAX_SIGNAL_LEVEL => '0',
        RadioTable::COLUMN_PRIVATE_NUMBER => '0',
    ];
    private const DEFAULT_NUMBER_FORMATTING = '@';

    private $phpSpreadsheetFactory;
    private $translator;

    public function __construct(Factory $phpSpreadsheetFactory, TranslatorInterface $translator)
    {
        $this->phpSpreadsheetFactory = $phpSpreadsheetFactory;
        $this->translator = $translator;
    }

    /**
     * @param RadioStation[] $radioStations
     */
    public function render(RadioTable $radioTable, array $radioStations): string
    {
        $spreadsheet = $this->getSpreadsheet($radioTable, $radioStations);

        $writer = $this->phpSpreadsheetFactory->createWriter(
            $spreadsheet,
            $this->getPhpSpreadsheetWriterType()
        );

        ob_start();
        $writer->save('php://output');

        return ob_get_clean();
    }

    abstract protected function getPhpSpreadsheetWriterType(): string;

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
                ->setFormatCode(self::COLUMN_NUMBER_FORMATTING[$column] ?? self::DEFAULT_NUMBER_FORMATTING)
            ;
        }

        return $spreadsheet;
    }

    private function getHeadings(RadioTable $radioTable): array
    {
        $headings = [];

        foreach ($radioTable->getColumns() as $column) {
            switch ($column) {
                case RadioTable::COLUMN_FREQUENCY:
                    $value = $this->getFrequencyLabel($radioTable->getFrequencyUnit());
                    break;

                case RadioTable::COLUMN_POWER:
                    $value = $this->getPowerLabel();
                    break;

                case RadioTable::COLUMN_DISTANCE:
                    $value = $this->getDistanceLabel();
                    break;

                case RadioTable::COLUMN_MAX_SIGNAL_LEVEL:
                    $value = $this->getMaxSignalLevelLabel($radioTable->getMaxSignalLevelUnit());
                    break;

                default:
                    $value = $this->translate('heading.' . $column);
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
                $value = $radioStation->{'get' . $column}();

                switch ($column) {
                    case RadioTable::COLUMN_TYPE:
                    case RadioTable::COLUMN_RECEPTION:
                        $value = $this->translate($column . '.' . $value);
                        break;

                    case RadioTable::COLUMN_POLARIZATION:
                        $value = $value ? $this->getPolarizationLabel($value) : '';
                        break;

                    case RadioTable::COLUMN_COMMENT:
                        // Remove \r. It comes from <textarea> and breaks some apps like iWork Numbers.
                        $value = str_replace("\r", '', $value);
                        break;

                    case RadioTable::COLUMN_RDS:
                        $rds = $value;

                        $value = $rds->getPs()[0][0] ?? '';
                        if ($value) {
                            $value = str_replace(' ', '_', $this->alignRDSFrame($value));
                        }
                        break;

                    case RadioTable::COLUMN_LOCALITY:
                        $locality = $value;

                        if (Locality::TYPE_COUNTRY === $locality->getType()) {
                            $value = $this->translate('locality.country');
                        }
                        else {
                            $value = $locality->getCity();

                            if (Locality::TYPE_NETWORK === $locality->getType()) {
                                $value .= ' ' . $this->translate('locality.network.abbr');
                            }
                        }
                        break;
                }

                $row[] = $value;
            }

            $data[] = $row;
        }

        return $data;
    }

    private function translate(string $id): string
    {
        return $this->translator->trans($id, [], 'radio_table');
    }
}
