<?php

use Chexwarrior\Bitbucket;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;

require __DIR__ . '/../vendor/autoload.php';

$loader = new \Twig\Loader\FilesystemLoader('../src/templates');
$twig = new \Twig\Environment($loader);
$app = AppFactory::create();

$app->get('/', function (Request $request, Response $response, $args) use ($twig) {
    $html = $twig->render('home.twig');
    $response->getBody()->write($html);
    return $response;
});

$app->get('/repositories', function (Request $request, Response $response, $args) use ($twig) {
    $params = $request->getQueryParams();
    $bitbucket = new Bitbucket($params['username'], $params['password']);
    $repositories = $bitbucket->getRepositories($params['workspace']);
    $repositories = $bitbucket->filterRepositoriesWithPipelines($params['workspace'], $repositories);
    $body = $response->getBody();
    $body->write($twig->render('repositories.twig', [
        'repos' => $repositories,
    ]));

    return $response;
});

$app->run();
