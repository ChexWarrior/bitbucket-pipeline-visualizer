<?php

namespace Chexwarrior;

use GuzzleHttp\Client;

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
    {   $repos = [];
        $url = self::BASE_PREFIX . "/repositories/$workspace?pagelen=100&fields=values.name,next";

        do {
            $response = $this->client->get($url);
            $json = json_decode($response->getBody()->getContents(), true);
            $repos = array_merge($repos, $json['values']);
            $url = !empty($json['next']) ? $json['next'] : null;
        } while ($url);

        return $repos;
    }
}