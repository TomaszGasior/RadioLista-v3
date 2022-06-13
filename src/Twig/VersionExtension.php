<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class VersionExtension extends AbstractExtension
{
    private $versionFilePath;

    private $version = null;

    public function __construct(string $versionFilePath)
    {
        $this->versionFilePath = $versionFilePath;
    }

    /**
     * @codeCoverageIgnore
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('app_version', [$this, 'getVersion']),
        ];
    }

    public function getVersion(): string
    {
        if ($this->version) {
            return $this->version;
        }

        try {
            $version = trim(file_get_contents($this->versionFilePath, false, null, 0, 10));
        }
        catch (\Throwable $e) {
            $version = 'version';
        }

        return $this->version = $version;
    }
}
