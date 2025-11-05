<?php

use Slim\Routing\RouteCollectorProxy;
use FormFlow\Middleware\AuthMiddleware;
use FormFlow\Middleware\RateLimitMiddleware;
use FormFlow\Controllers\AuthController;
use FormFlow\Controllers\FormController;
use FormFlow\Controllers\ResponseController;
use FormFlow\Controllers\SubscriptionController;
use FormFlow\Controllers\WebhookController;

// Instantiate controllers
$authController = new AuthController();
$formController = new FormController();
$responseController = new ResponseController();
$subscriptionController = new SubscriptionController();
$webhookController = new WebhookController();

// Public routes (no authentication required)
$app->group('/api', function (RouteCollectorProxy $group) use (
    $authController,
    $formController,
    $responseController,
    $webhookController
) {
    // Authentication routes
    $group->post('/auth/register', [$authController, 'register']);
    $group->post('/auth/login', [$authController, 'login']);

    // Public form routes
    $group->get('/forms/{slug}/public', [$formController, 'getPublic']);
    $group->post('/forms/{slug}/submit', [$responseController, 'submit']);

    // Stripe webhook (no auth, verified by signature)
    $group->post('/webhooks/stripe', [$webhookController, 'handleStripeWebhook']);
});

// Protected routes (authentication required)
$app->group('/api', function (RouteCollectorProxy $group) use (
    $authController,
    $formController,
    $responseController,
    $subscriptionController
) {
    // Auth routes
    $group->get('/auth/me', [$authController, 'me']);
    $group->post('/auth/refresh', [$authController, 'refresh']);

    // Form routes
    $group->get('/forms', [$formController, 'list']);
    $group->post('/forms', [$formController, 'create']);
    $group->get('/forms/{id}', [$formController, 'get']);
    $group->put('/forms/{id}', [$formController, 'update']);
    $group->delete('/forms/{id}', [$formController, 'delete']);

    // Response routes
    $group->get('/forms/{formId}/responses', [$responseController, 'list']);
    $group->get('/responses/{id}', [$responseController, 'get']);
    $group->delete('/responses/{id}', [$responseController, 'delete']);
    $group->get('/forms/{formId}/analytics', [$responseController, 'analytics']);

    // Subscription routes
    $group->get('/subscription/plans', [$subscriptionController, 'getPlans']);
    $group->get('/subscription/status', [$subscriptionController, 'getStatus']);
    $group->post('/subscription/create', [$subscriptionController, 'create']);
    $group->post('/subscription/cancel', [$subscriptionController, 'cancel']);
    $group->post('/subscription/update', [$subscriptionController, 'update']);
})->add(new AuthMiddleware());

// Add rate limiting to all API routes
$app->group('/api', function (RouteCollectorProxy $group) {
    // Routes are defined above
})->add(new RateLimitMiddleware());

// Health check
$app->get('/health', function ($request, $response) {
    $response->getBody()->write(json_encode([
        'status' => 'ok',
        'timestamp' => time()
    ]));
    return $response->withHeader('Content-Type', 'application/json');
});

// Root endpoint
$app->get('/', function ($request, $response) {
    $response->getBody()->write(json_encode([
        'name' => 'FormFlow API',
        'version' => '1.0.0',
        'documentation' => '/api/docs'
    ]));
    return $response->withHeader('Content-Type', 'application/json');
});
