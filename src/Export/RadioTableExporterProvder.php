<?php

namespace App\Export;

class RadioTableExporterProvder
{
    /**
     * @var ExporterInterface[]
     */
    private $exporters;

    public function __construct(iterable $exporters)
    {
        $this->exporters = $exporters;
    }

    public function getExporterForFileExtension(string $fileExtension): ExporterInterface
    {
        foreach ($this->exporters as $exporter) {
            $supportedFileExtension = strtolower(
                substr(strrchr(get_class($exporter), '\\'), 1, -1 * strlen('Exporter'))
            );

            if ($fileExtension === $supportedFileExtension) {
                return $exporter;
            }
        }

        throw new \RuntimeException(sprintf('Cannot find radio table exporter for "%s" file type.', $fileExtension));
    }
}
