<?php

namespace FormFlow\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class RateLimitMiddleware implements MiddlewareInterface
{
    private array $requests = [];
    private int $maxRequests;
    private int $windowSeconds;

    public function __construct(?int $maxRequests = null, ?int $windowSeconds = null)
    {
        $this->maxRequests = $maxRequests ?? (int)($_ENV['RATE_LIMIT_REQUESTS'] ?? 100);
        $this->windowSeconds = $windowSeconds ?? (int)($_ENV['RATE_LIMIT_WINDOW'] ?? 60);
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $ip = $this->getClientIp($request);
        $now = time();

        // Clean old requests
        if (isset($this->requests[$ip])) {
            $this->requests[$ip] = array_filter(
                $this->requests[$ip],
                fn($timestamp) => $timestamp > $now - $this->windowSeconds
            );
        } else {
            $this->requests[$ip] = [];
        }

        // Check rate limit
        if (count($this->requests[$ip]) >= $this->maxRequests) {
            $response = new \Slim\Psr7\Response();
            $response->getBody()->write(json_encode([
                'error' => true,
                'message' => 'Rate limit exceeded. Please try again later.'
            ]));

            return $response
                ->withStatus(429)
                ->withHeader('Content-Type', 'application/json')
                ->withHeader('Retry-After', (string)$this->windowSeconds);
        }

        // Add current request
        $this->requests[$ip][] = $now;

        return $handler->handle($request);
    }

    private function getClientIp(ServerRequestInterface $request): string
    {
        $serverParams = $request->getServerParams();

        if (!empty($serverParams['HTTP_X_FORWARDED_FOR'])) {
            return explode(',', $serverParams['HTTP_X_FORWARDED_FOR'])[0];
        }

        return $serverParams['REMOTE_ADDR'] ?? 'unknown';
    }
}
