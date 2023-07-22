<?php

namespace App\Twig;

use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class TrackerExtension extends AbstractExtension
{
    public function __construct(private array $settings) {}

    /**
     * @codeCoverageIgnore
     */
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
