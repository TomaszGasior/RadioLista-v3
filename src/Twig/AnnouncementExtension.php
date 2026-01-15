<?php

namespace App\Twig;

use Symfony\Component\Translation\LocaleSwitcher;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\HttpClient\Exception\ExceptionInterface as HttpClientExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Twig\Attribute\AsTwigFunction;

class AnnouncementExtension
{
    private const string REPOSITORY_NAME = 'TomaszGasior';
    private const string REPOSITORY_OWNER = 'RadioLista-v3';
    private const string ANNOUNCEMENTS_CATEGORY_ID = 'MDE4OkRpc2N1c3Npb25DYXRlZ29yeTMyOTg1NjAz';

    public function __construct(
        private CacheInterface $cache,
        private HttpClientInterface $githubClient,
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
        $response = $this->githubClient->request('POST', '/graphql', [
            'json' => [
                'query' => <<<'GRAPHQL'
                    query($owner:String!, $repository:String!, $categoryId:ID!) {
                        repository(owner: $owner, name: $repository) {
                            discussions(first: 1, categoryId: $categoryId, orderBy: {field: CREATED_AT, direction: DESC}) {
                                nodes{title, url, createdAt}
                            }
                        }
                    }
                GRAPHQL,
                'variables' => [
                    'owner' => self::REPOSITORY_NAME,
                    'repository' => self::REPOSITORY_OWNER,
                    'categoryId' => self::ANNOUNCEMENTS_CATEGORY_ID,
                ],
            ],
        ]);

        try {
            $data = $response->toArray();
        }
        catch (HttpClientExceptionInterface $e) {
            return null;
        }

        $data = $data['data']['repository']['discussions']['nodes'][0] ?? null;
        if (!$data || !($data['title'] ?? null) || !($data['url'] ?? null) || !($data['createdAt'] ?? null)) {
            return null;
        }

        if (strtotime($data['createdAt']) < strtotime('now - 7 days')) {
            return null;
        }

        return [
            'title' => $data['title'],
            'url' => $data['url'],
            'date' => $data['createdAt'],
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
