<?php

namespace App\Util;

use Dompdf\Adapter\CPDF;
use Dompdf\Css\Style;
use Dompdf\Dompdf;
use Dompdf\Options;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

class DompdfFactory
{
    public function __construct(
        #[Autowire('%kernel.project_dir%/assets/pdf')] private string $fontDir,
        #[Autowire('%kernel.cache_dir%/dompdf')] private string $cacheDir,
    ) {}

    /**
     * Don't inject Dompdf class instance through dependency injection.
     * It's stateful and should not be used to generate more than one document.
     */
    public function getDompdf(): Dompdf
    {
        if (!is_dir($this->cacheDir)) {
            mkdir($this->cacheDir, recursive: true);
        }

        $options = (new Options())
            ->setFontDir($this->fontDir)
            ->setFontCache($this->cacheDir)
            ->setTempDir($this->cacheDir)
            ->setAllowedRemoteHosts([])
        ;

        // Workaround for incorrect line-height calculation in Dompdf.
        // Without this, the line height is larger than in web browsers.
        // Do not use Options::setFontHeightRatio(),
        // as it breaks underlining and background sizing.
        Style::$default_line_height = 0.95;

        return new class($options) extends Dompdf
        {
            public function render(): void
            {
                parent::render();

                foreach ($this->getDom()->getElementsByTagName("meta") as $meta) {
                    $name = mb_strtolower($meta->getAttribute("name"));
                    $value = trim($meta->getAttribute("content"));

                    if ($name === 'generator') {
                        // Copy <meta name="generator"> to "Creator" field in PDF metadata.
                        $this->getCanvas()->add_info('Creator', $value);
                    }
                }

                $canvas = $this->getCanvas();
                if ($canvas instanceof CPDF) {
                    $cpdf = $canvas->get_cpdf();

                    // Remove "Producer" field from PDF metadata.
                    unset($cpdf->objects[$cpdf->infoObject]['info']['Producer']);
                }
            }
        };
    }
}
