<?php

use Chexwarrior\Bitbucket;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;

define('OAUTH_KEY', 'u8XF8hMJLJuyKbdmX2');
define('OAUTH_SECRET', 'c5tAAjVnChxBt9jupsdsQ9dT6MhNMEzm');

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
    $params = $request->getParsedBody();
    $bitbucket = new Bitbucket($params['username'], $params['password']);
    $repoJson = $bitbucket->getRepositories($params['workspace']);
    $body = $response->getBody();
    $body->write($twig->render('repositories.twig', [
        'repos' => $repoJson['values'],
    ]));

    return $response;
});

$app->run();
