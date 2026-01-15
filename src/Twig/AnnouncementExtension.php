<?php

namespace App\Twig;

use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Translation\LocaleSwitcher;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\HttpClient\Exception\ExceptionInterface as HttpClientExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Twig\Attribute\AsTwigFunction;

class AnnouncementExtension
{
    public function __construct(
        private CacheInterface $cache,
        private HttpClientInterface $httpClient,
        #[Autowire('%env(ANNOUNCEMENTS_FEED_URL)%')] private string $feedUrl,
        private LocaleSwitcher $localeSwitcher,
    ) {}

    #[AsTwigFunction('current_announcement')]
    public function currentAnnouncement(): ?array
    {
        $announcement = $this->cache->get('announcement', function (ItemInterface $item): ?array {
            $item->expiresAfter(3600);

            return $this->fetchAnnouncement();
        });

        if ($announcement) {
            $this->handleTitleLocale($announcement);
        }

        return $announcement;
    }

    /**
     * Fetch the most recent announcement from GitHub Discussions.
     */
    private function fetchAnnouncement(): ?array
    {
        $response = $this->httpClient->request('GET', $this->feedUrl);

        try {
            $data = $response->getContent();
        }
        catch (HttpClientExceptionInterface $e) {
            return null;
        }

        $xml = simplexml_load_string($data, options: LIBXML_NOWARNING | LIBXML_NOERROR | LIBXML_NONET);
        if (false === $xml) {
            return null;
        }

        $entry = $xml->entry[0] ?? null;
        if (!$entry) {
            return null;
        }

        $title = trim((string) ($entry->title ?? null));
        $url = trim((string) ($entry->link[0]['href'] ?? null));
        $date = trim((string) ($entry->published ?? null));

        if (!$title || !$url || !$date) {
            return null;
        }

        if (strtotime($date) < strtotime('now - 7 days')) {
            return null;
        }

        return [
            'title' => $title,
            'url' => $url,
            'date' => $date,
        ];
    }

    /**
     * Expected format for the announcement title on GitHub Discussions:
     * "Announcement title in English; Announcement title in Polish"
     */
    private function handleTitleLocale(array &$announcement): void
    {
        $title = explode(';', $announcement['title']);
        $locale = $this->localeSwitcher->getLocale();

        if (count($title) !== 2) {
            return;
        }

        $announcement['title'] = match ($locale) {
            'pl' => $title[1],
            default => $title[0],
        };

        // Remove emojis.
        $announcement['title'] = trim(preg_replace('/[\p{Extended_Pictographic}\x{FE0F}\x{200D}]+/u', '', $announcement['title']));
    }
}
