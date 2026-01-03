<?php

namespace App\Export;

use App\Entity\RadioTable;
use App\Util\DompdfFactory;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

class PdfExporter implements ExporterInterface
{
    public function __construct(
        private HtmlExporter $htmlExporter,
        private DompdfFactory $dompdfFactory,
        #[Autowire('%app.version%')] private string $version,
    ) {}

    public function render(string $format, RadioTable $radioTable, array $radioStations): string
    {
        $html = $this->htmlExporter->render('html', $radioTable, $radioStations);

        $dompdf = $this->dompdfFactory->getDompdf();

        $dompdf->getOptions()->setDpi(110);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->addInfo('Creator', 'RadioLista ' . $this->version);

        $dompdf->loadHtml($html);
        $dompdf->render();

        return $dompdf->output();
    }

    public function supports(string $format): bool
    {
        return 'pdf' === $format;
    }
}
