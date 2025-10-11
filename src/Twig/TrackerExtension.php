<?php

namespace App\Twig;

use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Twig\Attribute\AsTwigFunction;
use Twig\Environment;

class TrackerExtension
{
    public function __construct(
        #[Autowire('%env(json:TRACKER_SETTINGS)%')] private array $settings,
    ) {}

    #[AsTwigFunction('tracker_code', isSafe: ['html'])]
    public function printTrackerCode(Environment $twig): string
    {
        if (empty($this->settings['url']) || empty($this->settings['site_id'])) {
            return '';
        }

        return $twig->render('tracker.html.twig', [
            'url' => $this->settings['url'],
            'site_id' => $this->settings['site_id'],
            'user_session_dimension_id' => $this->settings['user_session_dimension_id'] ?? null,
        ]);
    }
}
