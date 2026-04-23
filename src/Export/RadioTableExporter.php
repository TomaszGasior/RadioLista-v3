<?php

namespace App\Export;

use App\Entity\RadioTable;
use App\Enum\ExportFormat;
use App\Repository\RadioTableRowRepository;
use RuntimeException;
use Symfony\Component\DependencyInjection\Attribute\AutowireIterator;

class RadioTableExporter
{
    /**
     * @param ExporterInterface[] $exporters
     */
    public function __construct(
        #[AutowireIterator(ExporterInterface::class)] private iterable $exporters,
        private RadioTableRowRepository $radioTableRowRepository,
    ) {}

    public function render(ExportFormat $format, RadioTable $radioTable): string
    {
        $rows = $this->radioTableRowRepository->findForRadioTable($radioTable);

        foreach ($this->exporters as $exporter) {
            if ($exporter->supports($format)) {
                return $exporter->render($format, $radioTable, $rows);
            }
        }

        throw new RuntimeException(sprintf('Format "%s" is not supported.', $format->value));
    }
}
