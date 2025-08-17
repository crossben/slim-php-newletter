<?php

use Slim\Factory\AppFactory;
use DI\Container;
use App\App;

require __DIR__ . '/../vendor/autoload.php';

$container = new Container();
AppFactory::setContainer($container);

$app = AppFactory::create();

// Define a route for subscribing
$app->post('/subscribe', [App::class, 'subscribe']);

// Define a route for unsubscribing
$app->delete('/unsubscribe/{email}', [App::class, 'unsubscribe']);

$app->run();
