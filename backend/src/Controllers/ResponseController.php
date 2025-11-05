<?php

namespace FormFlow\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use FormFlow\Models\Form;
use FormFlow\Models\Response as ResponseModel;
use FormFlow\Services\ValidationService;
use FormFlow\Services\EmailService;

class ResponseController
{
    private ResponseModel $responseModel;
    private Form $formModel;
    private ValidationService $validator;
    private EmailService $emailService;

    public function __construct()
    {
        $this->responseModel = new ResponseModel();
        $this->formModel = new Form();
        $this->validator = new ValidationService();
        $this->emailService = new EmailService();
    }

    public function submit(Request $request, Response $response, array $args): Response
    {
        $slug = $args['slug'];
        $data = $request->getParsedBody();
        $serverParams = $request->getServerParams();

        // Get form
        $form = $this->formModel->findBySlug($slug);

        if (!$form) {
            return $this->jsonResponse($response, [
                'error' => true,
                'message' => 'Form not found'
            ], 404);
        }

        // Validate submission
        $errors = $this->validator->validateFormSubmission($form['fields'], $data['answers'] ?? []);
        if (!empty($errors)) {
            return $this->jsonResponse($response, [
                'error' => true,
                'message' => 'Validation failed',
                'errors' => $errors
            ], 400);
        }

        // Prepare response data
        $responseData = [
            'answers' => $data['answers'],
            'metadata' => [
                'ipAddress' => $serverParams['REMOTE_ADDR'] ?? '',
                'userAgent' => $serverParams['HTTP_USER_AGENT'] ?? '',
                'referrer' => $serverParams['HTTP_REFERER'] ?? '',
                'completionTime' => $data['completionTime'] ?? 0,
                'paymentStatus' => null,
                'stripePaymentId' => null
            ]
        ];

        // Create response
        $formResponse = $this->responseModel->create((string)$form['_id'], $responseData);

        if (!$formResponse) {
            return $this->jsonResponse($response, [
                'error' => true,
                'message' => 'Failed to submit form'
            ], 500);
        }

        // Increment submissions count
        $this->formModel->incrementSubmissions((string)$form['_id']);

        // Send notification email if configured
        if (!empty($form['settings']['notifications']['email'])) {
            $this->emailService->sendFormSubmissionNotification(
                $form['settings']['notifications']['email'],
                $form['title'],
                $data['answers']
            );
        }

        return $this->jsonResponse($response, [
            'success' => true,
            'message' => 'Form submitted successfully',
            'responseId' => (string)$formResponse['_id'],
            'redirectUrl' => $form['settings']['redirectUrl'] ?? null
        ], 201);
    }

    public function list(Request $request, Response $response, array $args): Response
    {
        $userId = $request->getAttribute('userId');
        $formId = $args['formId'];
        $queryParams = $request->getQueryParams();

        // Check ownership
        if (!$this->formModel->belongsToUser($formId, $userId)) {
            return $this->jsonResponse($response, [
                'error' => true,
                'message' => 'Access denied'
            ], 403);
        }

        $options = [
            'limit' => (int)($queryParams['limit'] ?? 100),
            'skip' => (int)($queryParams['skip'] ?? 0)
        ];

        $responses = $this->responseModel->findByFormId($formId, $options);

        return $this->jsonResponse($response, [
            'success' => true,
            'responses' => array_map([$this, 'sanitizeResponse'], $responses),
            'total' => $this->responseModel->countByFormId($formId)
        ]);
    }

    public function get(Request $request, Response $response, array $args): Response
    {
        $userId = $request->getAttribute('userId');
        $responseId = $args['id'];

        $formResponse = $this->responseModel->findById($responseId);

        if (!$formResponse) {
            return $this->jsonResponse($response, [
                'error' => true,
                'message' => 'Response not found'
            ], 404);
        }

        // Check ownership
        $form = $this->formModel->findById((string)$formResponse['formId']);
        if ((string)$form['userId'] !== $userId) {
            return $this->jsonResponse($response, [
                'error' => true,
                'message' => 'Access denied'
            ], 403);
        }

        return $this->jsonResponse($response, [
            'success' => true,
            'response' => $this->sanitizeResponse($formResponse)
        ]);
    }

    public function delete(Request $request, Response $response, array $args): Response
    {
        $userId = $request->getAttribute('userId');
        $responseId = $args['id'];

        $formResponse = $this->responseModel->findById($responseId);

        if (!$formResponse) {
            return $this->jsonResponse($response, [
                'error' => true,
                'message' => 'Response not found'
            ], 404);
        }

        // Check ownership
        $form = $this->formModel->findById((string)$formResponse['formId']);
        if ((string)$form['userId'] !== $userId) {
            return $this->jsonResponse($response, [
                'error' => true,
                'message' => 'Access denied'
            ], 403);
        }

        $success = $this->responseModel->delete($responseId, (string)$formResponse['formId']);

        if (!$success) {
            return $this->jsonResponse($response, [
                'error' => true,
                'message' => 'Failed to delete response'
            ], 500);
        }

        return $this->jsonResponse($response, [
            'success' => true,
            'message' => 'Response deleted successfully'
        ]);
    }

    public function analytics(Request $request, Response $response, array $args): Response
    {
        $userId = $request->getAttribute('userId');
        $formId = $args['formId'];

        // Check ownership
        if (!$this->formModel->belongsToUser($formId, $userId)) {
            return $this->jsonResponse($response, [
                'error' => true,
                'message' => 'Access denied'
            ], 403);
        }

        $analytics = $this->responseModel->getAnalytics($formId);

        return $this->jsonResponse($response, [
            'success' => true,
            'analytics' => $analytics
        ]);
    }

    private function sanitizeResponse(array $response): array
    {
        return [
            'id' => (string)$response['_id'],
            'formId' => (string)$response['formId'],
            'answers' => $response['answers'],
            'metadata' => $response['metadata'],
            'submittedAt' => $response['submittedAt']->toDateTime()->format('Y-m-d H:i:s')
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
