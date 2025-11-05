<?php

namespace FormFlow\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use FormFlow\Models\User;
use FormFlow\Services\ValidationService;
use FormFlow\Services\EmailService;
use Firebase\JWT\JWT;

class AuthController
{
    private User $userModel;
    private ValidationService $validator;
    private EmailService $emailService;
    private array $jwtConfig;

    public function __construct()
    {
        $this->userModel = new User();
        $this->validator = new ValidationService();
        $this->emailService = new EmailService();
        $this->jwtConfig = require __DIR__ . '/../../config/jwt.php';
    }

    public function register(Request $request, Response $response): Response
    {
        $data = $request->getParsedBody();

        // Validate input
        $errors = $this->validator->validateRegistration($data);
        if (!empty($errors)) {
            return $this->jsonResponse($response, [
                'error' => true,
                'message' => 'Validation failed',
                'errors' => $errors
            ], 400);
        }

        // Check if email exists
        if ($this->userModel->emailExists($data['email'])) {
            return $this->jsonResponse($response, [
                'error' => true,
                'message' => 'Email already exists'
            ], 409);
        }

        // Create user
        $user = $this->userModel->create($data);

        if (!$user) {
            return $this->jsonResponse($response, [
                'error' => true,
                'message' => 'Failed to create user'
            ], 500);
        }

        // Send welcome email
        $this->emailService->sendWelcomeEmail($user['email'], $user['name']);

        // Generate JWT token
        $token = $this->generateToken($user);

        return $this->jsonResponse($response, [
            'success' => true,
            'message' => 'Registration successful',
            'token' => $token,
            'user' => $this->sanitizeUser($user)
        ], 201);
    }

    public function login(Request $request, Response $response): Response
    {
        $data = $request->getParsedBody();

        // Validate input
        $errors = $this->validator->validateLogin($data);
        if (!empty($errors)) {
            return $this->jsonResponse($response, [
                'error' => true,
                'message' => 'Validation failed',
                'errors' => $errors
            ], 400);
        }

        // Find user
        $user = $this->userModel->findByEmail($data['email']);

        if (!$user || !$this->userModel->verifyPassword($data['password'], $user['password'])) {
            return $this->jsonResponse($response, [
                'error' => true,
                'message' => 'Invalid credentials'
            ], 401);
        }

        // Generate JWT token
        $token = $this->generateToken($user);

        return $this->jsonResponse($response, [
            'success' => true,
            'message' => 'Login successful',
            'token' => $token,
            'user' => $this->sanitizeUser($user)
        ]);
    }

    public function me(Request $request, Response $response): Response
    {
        $userId = $request->getAttribute('userId');
        $user = $this->userModel->findById($userId);

        if (!$user) {
            return $this->jsonResponse($response, [
                'error' => true,
                'message' => 'User not found'
            ], 404);
        }

        return $this->jsonResponse($response, [
            'success' => true,
            'user' => $this->sanitizeUser($user)
        ]);
    }

    public function refresh(Request $request, Response $response): Response
    {
        $userId = $request->getAttribute('userId');
        $user = $this->userModel->findById($userId);

        if (!$user) {
            return $this->jsonResponse($response, [
                'error' => true,
                'message' => 'User not found'
            ], 404);
        }

        $token = $this->generateToken($user);

        return $this->jsonResponse($response, [
            'success' => true,
            'token' => $token
        ]);
    }

    private function generateToken(array $user): string
    {
        $issuedAt = time();
        $expirationTime = $issuedAt + $this->jwtConfig['expiration'];

        $payload = [
            'iat' => $issuedAt,
            'exp' => $expirationTime,
            'userId' => (string)$user['_id'],
            'email' => $user['email']
        ];

        return JWT::encode($payload, $this->jwtConfig['secret'], $this->jwtConfig['algorithm']);
    }

    private function sanitizeUser(array $user): array
    {
        return [
            'id' => (string)$user['_id'],
            'email' => $user['email'],
            'name' => $user['name'],
            'company' => $user['company'],
            'subscription' => $user['subscription'],
            'createdAt' => $user['createdAt']->toDateTime()->format('Y-m-d H:i:s')
        ];
    }

    private function jsonResponse(Response $response, array $data, int $status = 200): Response
    {
        $response->getBody()->write(json_encode($data));
        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus($status);
    }
}
