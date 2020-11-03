<?php

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

$app->run();
