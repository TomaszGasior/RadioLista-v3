<?php

namespace App\Twig;

use App\Util\ContentSecurityPolicyHandler;
use Twig\Attribute\AsTwigFilter;
use Twig\Attribute\AsTwigFunction;
use Twig\Environment;
use Twig\Extension\CoreExtension;
use Twig\Extra\Intl\IntlExtension;

class AppExtension
{
    public function __construct(private ContentSecurityPolicyHandler $cspHandler) {}

    #[AsTwigFilter('format_date_html', isSafe: ['html'])]
    public function formatDateHTML(Environment $twig, $date, ?string $dateFormat = 'long', ...$args): string
    {
        $intlExtension = $twig->getExtension(IntlExtension::class);
        $coreExtension = $twig->getExtension(CoreExtension::class);

        $date = $coreExtension->convertDate($date);

        return sprintf(
            '<time datetime="%s">%s</time>',
            $date->format('Y-m-d'),
            $intlExtension->formatDate($twig, $date, $dateFormat, ...$args)
        );
    }

    #[AsTwigFunction('csp_directive')]
    public function cspDirective(string $value, string ...$directives): void
    {
        $this->cspHandler->addDirective($value, ...$directives);
    }

    #[AsTwigFunction('csp_nonce')]
    public function cspNonce(string $directive): string
    {
        return $this->cspHandler->addNonce($directive);
    }
}
