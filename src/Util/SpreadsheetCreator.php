<?php

namespace App\Util;

use App\Entity\Enum\RadioTable\Column;
use App\Entity\RadioTable;
use App\Model\Row;
use App\Util\RadioStationRdsTrait;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Contracts\Translation\TranslatorInterface;

class SpreadsheetCreator
{
    use RadioStationRdsTrait;

    public function __construct(
        private TranslatorInterface $translator,
        #[Autowire('%app.version%')] private string $version,
        private RequestStack $requestStack,
    ) {}

    /**
     * @param Row[] $rows
     */
    public function createSpreadsheet(RadioTable $radioTable, array $rows): Spreadsheet
    {
        $spreadsheet = new Spreadsheet;
        $worksheet = $spreadsheet->getActiveSheet();

        $worksheet
            ->fromArray($this->getHeadings($radioTable))
            ->fromArray($this->getRows($radioTable, $rows), null, 'A2')
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
                ->setFormatCode(match ($column) {
                    Column::FREQUENCY => '0.000',
                    Column::POWER => '0.000',
                    Column::QUALITY => '0',
                    Column::DISTANCE => '0',
                    Column::MAX_SIGNAL_LEVEL => '0',
                    Column::PRIVATE_NUMBER => '0',
                    default => '@',
                })
            ;
        }

        $spreadsheet
            ->getProperties()
            ->setTitle($radioTable->getName())
            ->setCreator($this->getCreatorMetadata())
            ->setLastModifiedBy('')
        ;

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
     * @param Row[] $rows
     */
    private function getRows(RadioTable $radioTable, array $rows): array
    {
        $data = [];

        foreach ($rows as $row) {
            $result = [];

            foreach ($radioTable->getColumns() as $column) {
                $result[] = match ($column) {
                    Column::TYPE =>
                        $this->translate('type.' . $row->type->value),
                    Column::RECEPTION =>
                        $this->translate('reception.' . $row->reception->value),
                    Column::POLARIZATION =>
                        $row->polarization?->getLabel() ?: '',
                    Column::QUALITY =>
                        $row->quality->getLabel(),
                    Column::DAB_CHANNEL =>
                        $row->dabChannel->value ?? '',
                    Column::COMMENT =>
                        // Remove \r. It comes from <textarea> and breaks some apps like iWork Numbers in CSV format.
                        str_replace("\r", '', (string) $row->comment),
                    Column::RDS =>
                        str_replace(' ', '_', $this->alignRDSFrame($row->rds->getPs()[0][0] ?? '')),
                    default =>
                        $row->{$column->value},
                };
            }

            $data[] = $result;
        }

        return $data;
    }

    private function translate(string $id): string
    {
        return $this->translator->trans($id, [], 'radio_table');
    }

    private function getCreatorMetadata(): string
    {
        return sprintf(
            'RadioLista %s — %s',
            $this->version,
            $this->requestStack->getCurrentRequest()->getSchemeAndHttpHost()
        );
    }
}
