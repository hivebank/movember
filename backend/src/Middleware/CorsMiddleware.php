<?php

namespace FormFlow\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class CorsMiddleware implements MiddlewareInterface
{
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $allowedOrigins = config('cors.allowed_origins', ['*']);
        $origin = $request->getHeaderLine('Origin');

        if ($request->getMethod() === 'OPTIONS') {
            $response = new \Slim\Psr7\Response();
        } else {
            $response = $handler->handle($request);
        }

        // Set CORS headers
        if (in_array('*', $allowedOrigins) || in_array($origin, $allowedOrigins)) {
            $response = $response
                ->withHeader('Access-Control-Allow-Origin', $origin ?: '*')
                ->withHeader('Access-Control-Allow-Credentials', 'true')
                ->withHeader('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With')
                ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS, PATCH')
                ->withHeader('Access-Control-Max-Age', '3600');
        }

        return $response;
    }
}
