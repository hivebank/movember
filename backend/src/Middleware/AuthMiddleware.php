<?php

namespace FormFlow\Middleware;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class AuthMiddleware implements MiddlewareInterface
{
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $config = require __DIR__ . '/../../config/jwt.php';

        // Get token from Authorization header
        $authHeader = $request->getHeaderLine('Authorization');

        if (!$authHeader || !preg_match('/Bearer\s+(.*)$/i', $authHeader, $matches)) {
            return $this->unauthorizedResponse();
        }

        $token = $matches[1];

        try {
            $decoded = JWT::decode($token, new Key($config['secret'], $config['algorithm']));

            // Add user data to request attributes
            $request = $request->withAttribute('userId', $decoded->userId);
            $request = $request->withAttribute('userEmail', $decoded->email);

            return $handler->handle($request);
        } catch (\Exception $e) {
            return $this->unauthorizedResponse($e->getMessage());
        }
    }

    private function unauthorizedResponse(string $message = 'Unauthorized'): ResponseInterface
    {
        $response = new \Slim\Psr7\Response();
        $response->getBody()->write(json_encode([
            'error' => true,
            'message' => $message
        ]));

        return $response
            ->withStatus(401)
            ->withHeader('Content-Type', 'application/json');
    }
}
