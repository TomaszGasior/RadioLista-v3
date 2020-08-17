<?php

namespace App\Twig;

use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class TrackerExtension extends AbstractExtension
{
    private $domain;
    private $siteId;

    public function __construct(string $domain, int $siteId)
    {
        $this->domain = $domain;
        $this->siteId = $siteId;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('tracker_code', [$this, 'printTrackerCode'], [
                'needs_environment' => true,
                'is_safe' => ['html'],
            ]),
        ];
    }

    public function printTrackerCode(Environment $twig): string
    {
        return $twig->render('tracker.html.twig', [
            'domain' => $this->domain,
            'site_id' => $this->siteId,
        ]);
    }
}
