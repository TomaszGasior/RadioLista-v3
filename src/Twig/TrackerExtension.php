<?php

namespace App\Twig;

use App\Entity\User;
use Symfony\Component\Security\Core\Security;
use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class TrackerExtension extends AbstractExtension
{
    private $domain;
    private $siteId;
    private $security;

    public function __construct(string $domain, int $siteId, Security $security)
    {
        $this->domain = $domain;
        $this->siteId = $siteId;
        $this->security = $security;
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
        $user = $this->security->getUser();

        return $twig->render('tracker.html.twig', [
            'domain' => $this->domain,
            'site_id' => $this->siteId,
            'user_id' => $user instanceof User ? $user->getId() : null,
        ]);
    }
}
