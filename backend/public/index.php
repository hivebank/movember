<?php

use Slim\Factory\AppFactory;
use FormFlow\Middleware\CorsMiddleware;
use FormFlow\Middleware\AuthMiddleware;

require __DIR__ . '/../vendor/autoload.php';

// Load configuration
$config = require __DIR__ . '/../config/config.php';

// Make config globally accessible
define('CONFIG', $config);

// Helper function to get config values
if (!function_exists('config')) {
    function config($key, $default = null) {
        $keys = explode('.', $key);
        $value = CONFIG;

        foreach ($keys as $k) {
            if (!isset($value[$k])) {
                return $default;
            }
            $value = $value[$k];
        }

        return $value;
    }
}

// Create Slim app
$app = AppFactory::create();

// Add error middleware
$app->addErrorMiddleware(
    config('app.debug', false),
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
