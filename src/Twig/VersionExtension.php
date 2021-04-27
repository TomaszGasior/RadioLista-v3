<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class VersionExtension extends AbstractExtension
{
    private $environment;
    private $versionFilePath;
    private $gitDirPath;

    private $version = null;

    public function __construct(string $environment, string $versionFilePath, string $gitDirPath)
    {
        $this->environment = $environment;
        $this->versionFilePath = $versionFilePath;
        $this->gitDirPath = $gitDirPath;
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
            if ('prod' === $this->environment) {
                $version = '—';
            }
            else {
                try {
                    $version = 'l:' . $this->getCommitHashFromGitDir();
                }
                catch (\Throwable $e) {
                    $version = 'local';
                }
            }
        }

        return $this->version = $version;
    }

    private function getCommitHashFromGitDir(): string
    {
        $head = file_get_contents($this->gitDirPath . '/HEAD');
        $head = trim(str_replace('ref: ', '', $head, $count));

        $commit = (0 === $count) ? $head : file_get_contents($this->gitDirPath . '/' . $head);
        $commit = substr($commit, 0, 7);

        return $commit;
    }
}
