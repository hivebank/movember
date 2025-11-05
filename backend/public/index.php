<?php

use Slim\Factory\AppFactory;
use Dotenv\Dotenv;
use FormFlow\Middleware\CorsMiddleware;
use FormFlow\Middleware\AuthMiddleware;

require __DIR__ . '/../vendor/autoload.php';

// Load environment variables
$dotenv = Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->safeLoad();

// Create Slim app
$app = AppFactory::create();

// Add error middleware
$app->addErrorMiddleware(
    $_ENV['APP_DEBUG'] === 'true',
    true,
    true
);

// Add body parsing middleware
$app->addBodyParsingMiddleware();

// Add CORS middleware
$app->add(new CorsMiddleware());

// Load routes
require __DIR__ . '/../src/routes.php';

$app->run();
