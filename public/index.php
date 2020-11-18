<?php

use Chexwarrior\Bitbucket;
use Chexwarrior\FakeBitbucket;
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
    $body = $response->getBody();
    //$bitbucket = new Bitbucket($params['username'], $params['password']);
    $bitbucket = new FakeBitbucket();

    try {
        $repositories = $bitbucket->getRepositories();
        //$repositories = $bitbucket->getRepositories($params['workspace']);
        // $repositories = $bitbucket->filterRepositoriesWithPipelines($params['workspace'], $repositories);
        $body->write($twig->render('repositories.twig', [
            'repos' => $repositories,
        ]));
    } catch (Exception $e) {
        $body->write($twig->render('error.twig', [
            'message' => 'There was an error communicating with the Bitbucket API, please double check your credentials',
        ]));
    }



    return $response;
});

$app->get('/pipelines', function (Request $request, Response $response, $args) use ($twig) {
    $params = $request->getQueryParams();
    $body = $response->getBody();
    $bitbucket = new FakeBitbucket();
    $pipelines = $bitbucket->getPipelines();
    $body->write($twig->render('pipelines.twig', [
        'pipelines' => $pipelines,
    ]));

    return $response;
});

$app->run();
