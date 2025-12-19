<?php

namespace App\Export;

use App\Entity\RadioTable;
use App\Enum\ExportFormat;
use App\Repository\RadioStationRepository;
use RuntimeException;
use Symfony\Component\DependencyInjection\Attribute\AutowireIterator;

class RadioTableExporter
{
    /**
     * @param ExporterInterface[] $exporters
     */
    public function __construct(
        #[AutowireIterator(ExporterInterface::class)] private iterable $exporters,
        private RadioStationRepository $radioStationRepository,
    ) {}

    public function render(ExportFormat $format, RadioTable $radioTable): string
    {
        $radioStations = $this->radioStationRepository->findForRadioTable($radioTable);

        foreach ($this->exporters as $exporter) {
            if ($exporter->supports($format)) {
                return $exporter->render($format, $radioTable, $radioStations);
            }
        }

        throw new RuntimeException(sprintf('Format "%s" is not supported.', $format->value));
    }
}
