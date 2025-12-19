<?php

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpClient\HttpClient;

#[AsCommand(
    name: 'app:try-github-discussions-wip',
)]
class TryGithubDiscussionsWipCommand extends Command
{
    public function __construct()
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $token = $_ENV['GITHUB_TOKEN'];
        // GITHUB_CATEGORY_ID='MDE4OkRpc2N1c3Npb25DYXRlZ29yeTMyOTg1NjAz'
        $categoryId = $_ENV['GITHUB_CATEGORY_ID'];

        $client = HttpClient::create([
            'headers' => [
                'Authorization' => 'Bearer ' . $token,
                'Content-Type'  => 'application/json',
            ],
        ]);

        $query = <<<GRAPHQL
        query {
        repository(owner: "TomaszGasior", name: "RadioLista-v3") {
            discussions(first: 100, orderBy: {field: CREATED_AT, direction: DESC}) {
            nodes {
                title
                url
                createdAt
                category {
                id
                }
            }
            }
        }
        }
        GRAPHQL;

        $response = $client->request('POST', 'https://api.github.com/graphql', [
            'json' => ['query' => $query],
        ]);

        $data = $response->toArray();

        $discussions = [];
        foreach ($data['data']['repository']['discussions']['nodes'] as $discussion) {
            if ($discussion['category']['id'] === $categoryId) {
                $discussions[] = $discussion;
            }

            if (count($discussions) === 10) {
                break;
            }
        }

        dump($discussions);
        return 1;
    }
}
