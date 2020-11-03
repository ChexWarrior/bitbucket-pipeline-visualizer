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
    {
        $response = $this->client->get(self::BASE_PREFIX . "/repositories/$workspace?fields=values.name,next");
        // TODO: Handle page length and grabbing all pages
        return json_decode($response->getBody()->getContents(), true);
    }
}