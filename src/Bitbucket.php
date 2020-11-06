<?php

namespace Chexwarrior;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Pool;
use GuzzleHttp\Promise;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;

class Bitbucket
{
    private Client $client;

    private const BASE_PREFIX = '/2.0';

    public function __construct(string $username, string $password, array $clientOptions = [])
    {
        $defaultOptions = [
            'base_uri' => 'https://api.bitbucket.org',
            'auth' => [$username, $password],
        ];

        $options = array_merge($defaultOptions, $clientOptions);
        $this->client = new Client($options);
    }


    public function getRepositories(string $workspace): array
    {
        $repos = [];
        $url = self::BASE_PREFIX . "/repositories/$workspace?pagelen=100&fields=values.name,next";

        do {
            $response = $this->client->get($url);
            $json = json_decode($response->getBody()->getContents(), true);
            $repos = array_merge($repos, $json['values']);
            $url = !empty($json['next']) ? $json['next'] : null;
        } while ($url);

        return $repos;
    }

    public function filterRepositoriesWithPipelines(string $workspace, array $repositories): array {
        $repos = [];
        $promises = [];

        foreach($repositories as $r) {
            $name = $r['name'];
            $promises[$name] = $this->client->getAsync(
                self::BASE_PREFIX . "/repositories/$workspace/$name/pipelines_config"
            );
        }

        $responses = Promise\Utils::settle($promises)->wait();

        foreach ($responses as $key => $res) {
            if ($res['state'] === 'fulfilled') {
                $repos[] = $key;
            }
        }

        return $repos;
    }
}