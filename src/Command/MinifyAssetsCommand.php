<?php

namespace App\Command;

use MatthiasMullie\Minify;
use Psr\Container\ContainerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Finder\Finder;

class MinifyAssetsCommand extends Command
{
    protected static $defaultName = 'app:minify-assets';

    protected function configure(): void
    {
        $this
            ->setDescription('Minify CSS and JS files in public/assets')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        $assetsPath = $this->getAssetsDirectoryPath();

        $finder = (new Finder())
            ->files()->in($assetsPath)->name('*.css');

        foreach ($finder as $file) {
            $minifier = new Minify\CSS($file->getRealPath());
            $minifier->minify();
        }

        $finder = (new Finder())
            ->files()->in($assetsPath)->name('*.js');

        foreach ($finder as $file) {
            $minifier = new Minify\JS($file->getRealPath());
            $minifier->minify();
        }
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
}
