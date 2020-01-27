<?php

namespace App\Command;

use MatthiasMullie\Minify;
use Psr\Container\ContainerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;

class MinifyAssetsCommand extends Command
{
    // Keep in sync with prod/framework.yaml.
    private const MINIFIED_PREFIX = 'minified-';

    protected static $defaultName = 'app:minify-assets';

    protected function configure(): void
    {
        $this
            ->setDescription('Minify CSS and JS files in public/assets')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $assetsPath = $this->getAssetsDirectoryPath();

        $finder = (new Finder)
            ->files()->depth(0)->in($assetsPath)
            ->name('*.css')->notName(self::MINIFIED_PREFIX . '*');

        foreach ($finder as $file) {
            $path = $file->getRealPath();
            (new Minify\CSS($path))->minify($this->addPrefixToPath($path));
        }

        $finder = (new Finder)
            ->files()->depth(0)->in($assetsPath)
            ->name('*.js')->notName(self::MINIFIED_PREFIX . '*');

        foreach ($finder as $file) {
            $path = $file->getRealPath();
            (new Minify\JS($path))->minify($this->addPrefixToPath($path));
        }

        return 0;
    }

    private function getAssetsDirectoryPath(): string
    {
        $kernel = $this->getApplication()->getKernel();

        return $kernel->getProjectDir() . '/' .
               $this->getPublicDirectory($kernel->getContainer()) . '/assets';
    }

    /**
     * This function is copied from AssetsInstallCommand in FrameworkBundle.
     */
    private function getPublicDirectory(ContainerInterface $container): string
    {
        $defaultPublicDir = 'public';

        if (!$container->hasParameter('kernel.project_dir')) {
            return $defaultPublicDir;
        }

        $composerFilePath = $container->getParameter('kernel.project_dir').'/composer.json';

        if (!file_exists($composerFilePath)) {
            return $defaultPublicDir;
        }

        $composerConfig = json_decode(file_get_contents($composerFilePath), true);

        if (isset($composerConfig['extra']['public-dir'])) {
            return $composerConfig['extra']['public-dir'];
        }

        return $defaultPublicDir;
    }

    private function addPrefixToPath($path): string
    {
        return dirname($path) . '/' . self::MINIFIED_PREFIX . basename($path);
    }
}
