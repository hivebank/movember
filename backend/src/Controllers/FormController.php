<?php

namespace FormFlow\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use FormFlow\Models\Form;
use FormFlow\Models\User;
use FormFlow\Services\ValidationService;

class FormController
{
    private Form $formModel;
    private User $userModel;
    private ValidationService $validator;

    public function __construct()
    {
        $this->formModel = new Form();
        $this->userModel = new User();
        $this->validator = new ValidationService();
    }

    public function list(Request $request, Response $response): Response
    {
        $userId = $request->getAttribute('userId');
        $queryParams = $request->getQueryParams();

        $filters = [];
        if (!empty($queryParams['status'])) {
            $filters['status'] = $queryParams['status'];
        }

        $forms = $this->formModel->findByUserId($userId, $filters);

        return $this->jsonResponse($response, [
            'success' => true,
            'forms' => array_map([$this, 'sanitizeForm'], $forms)
        ]);
    }

    public function create(Request $request, Response $response): Response
    {
        $userId = $request->getAttribute('userId');
        $data = $request->getParsedBody();

        // Check plan limits
        $user = $this->userModel->findById($userId);
        $stripeConfig = require __DIR__ . '/../../config/stripe.php';
        $plan = $stripeConfig['plans'][$user['subscription']['plan']];

        if ($plan['forms_limit'] !== null) {
            $formsCount = $this->formModel->countByUserId($userId);
            if ($formsCount >= $plan['forms_limit']) {
                return $this->jsonResponse($response, [
                    'error' => true,
                    'message' => 'Form limit reached. Please upgrade your plan.'
                ], 403);
            }
        }

        // Validate input
        $errors = $this->validator->validateForm($data);
        if (!empty($errors)) {
            return $this->jsonResponse($response, [
                'error' => true,
                'message' => 'Validation failed',
                'errors' => $errors
            ], 400);
        }

        // Create form
        $form = $this->formModel->create($userId, $data);

        if (!$form) {
            return $this->jsonResponse($response, [
                'error' => true,
                'message' => 'Failed to create form'
            ], 500);
        }

        return $this->jsonResponse($response, [
            'success' => true,
            'message' => 'Form created successfully',
            'form' => $this->sanitizeForm($form)
        ], 201);
    }

    public function get(Request $request, Response $response, array $args): Response
    {
        $userId = $request->getAttribute('userId');
        $formId = $args['id'];

        $form = $this->formModel->findById($formId);

        if (!$form) {
            return $this->jsonResponse($response, [
                'error' => true,
                'message' => 'Form not found'
            ], 404);
        }

        // Check ownership
        if ((string)$form['userId'] !== $userId) {
            return $this->jsonResponse($response, [
                'error' => true,
                'message' => 'Access denied'
            ], 403);
        }

        return $this->jsonResponse($response, [
            'success' => true,
            'form' => $this->sanitizeForm($form)
        ]);
    }

    public function update(Request $request, Response $response, array $args): Response
    {
        $userId = $request->getAttribute('userId');
        $formId = $args['id'];
        $data = $request->getParsedBody();

        // Check ownership
        if (!$this->formModel->belongsToUser($formId, $userId)) {
            return $this->jsonResponse($response, [
                'error' => true,
                'message' => 'Access denied'
            ], 403);
        }

        // Update form
        $success = $this->formModel->update($formId, $data);

        if (!$success) {
            return $this->jsonResponse($response, [
                'error' => true,
                'message' => 'Failed to update form'
            ], 500);
        }

        $form = $this->formModel->findById($formId);

        return $this->jsonResponse($response, [
            'success' => true,
            'message' => 'Form updated successfully',
            'form' => $this->sanitizeForm($form)
        ]);
    }

    public function delete(Request $request, Response $response, array $args): Response
    {
        $userId = $request->getAttribute('userId');
        $formId = $args['id'];

        // Delete form
        $success = $this->formModel->delete($formId, $userId);

        if (!$success) {
            return $this->jsonResponse($response, [
                'error' => true,
                'message' => 'Failed to delete form or access denied'
            ], 500);
        }

        return $this->jsonResponse($response, [
            'success' => true,
            'message' => 'Form deleted successfully'
        ]);
    }

    public function getPublic(Request $request, Response $response, array $args): Response
    {
        $slug = $args['slug'];
        $form = $this->formModel->findBySlug($slug);

        if (!$form) {
            return $this->jsonResponse($response, [
                'error' => true,
                'message' => 'Form not found'
            ], 404);
        }

        // Increment views
        $this->formModel->incrementViews((string)$form['_id']);

        return $this->jsonResponse($response, [
            'success' => true,
            'form' => $this->sanitizePublicForm($form)
        ]);
    }

    private function sanitizeForm(array $form): array
    {
        return [
            'id' => (string)$form['_id'],
            'title' => $form['title'],
            'description' => $form['description'],
            'slug' => $form['slug'],
            'fields' => $form['fields'],
            'settings' => $form['settings'],
            'status' => $form['status'],
            'views' => $form['views'],
            'submissions' => $form['submissions'],
            'createdAt' => $form['createdAt']->toDateTime()->format('Y-m-d H:i:s'),
            'updatedAt' => $form['updatedAt']->toDateTime()->format('Y-m-d H:i:s')
        ];
    }

    private function sanitizePublicForm(array $form): array
    {
        return [
            'id' => (string)$form['_id'],
            'title' => $form['title'],
            'description' => $form['description'],
            'fields' => $form['fields'],
            'settings' => [
                'theme' => $form['settings']['theme'] ?? 'default',
                'submitText' => $form['settings']['submitText'] ?? 'Submit',
                'requirePayment' => $form['settings']['requirePayment'] ?? false,
                'paymentAmount' => $form['settings']['paymentAmount'] ?? 0
            ]
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
