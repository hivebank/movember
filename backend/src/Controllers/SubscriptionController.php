<?php

namespace FormFlow\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use FormFlow\Models\User;
use FormFlow\Models\Subscription;
use FormFlow\Services\StripeService;
use FormFlow\Services\EmailService;

class SubscriptionController
{
    private User $userModel;
    private Subscription $subscriptionModel;
    private StripeService $stripeService;
    private EmailService $emailService;

    public function __construct()
    {
        $this->userModel = new User();
        $this->subscriptionModel = new Subscription();
        $this->stripeService = new StripeService();
        $this->emailService = new EmailService();
    }

    public function getPlans(Request $request, Response $response): Response
    {
        $plans = $this->stripeService->getPlans();

        return $this->jsonResponse($response, [
            'success' => true,
            'plans' => $plans
        ]);
    }

    public function getStatus(Request $request, Response $response): Response
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
            'subscription' => $user['subscription']
        ]);
    }

    public function create(Request $request, Response $response): Response
    {
        $userId = $request->getAttribute('userId');
        $data = $request->getParsedBody();
        $user = $this->userModel->findById($userId);

        if (!$user) {
            return $this->jsonResponse($response, [
                'error' => true,
                'message' => 'User not found'
            ], 404);
        }

        // Validate plan
        $plan = $data['plan'] ?? '';
        if (!in_array($plan, ['starter', 'pro', 'enterprise'])) {
            return $this->jsonResponse($response, [
                'error' => true,
                'message' => 'Invalid plan'
            ], 400);
        }

        // Get or create Stripe customer
        $stripeCustomerId = $user['subscription']['stripeCustomerId'];

        if (!$stripeCustomerId) {
            $customer = $this->stripeService->createCustomer($user['email'], $user['name']);

            if (!$customer) {
                return $this->jsonResponse($response, [
                    'error' => true,
                    'message' => 'Failed to create Stripe customer'
                ], 500);
            }

            $stripeCustomerId = $customer->id;

            // Update user with customer ID
            $this->userModel->updateSubscription($userId, [
                'plan' => $user['subscription']['plan'],
                'status' => $user['subscription']['status'],
                'stripeCustomerId' => $stripeCustomerId,
                'stripeSubscriptionId' => null,
                'currentPeriodEnd' => null
            ]);
        }

        // Get price ID
        $priceId = $this->stripeService->getPriceId($plan);

        if (!$priceId) {
            return $this->jsonResponse($response, [
                'error' => true,
                'message' => 'Price ID not configured for this plan'
            ], 500);
        }

        // Create checkout session
        $successUrl = config('app.url', 'http://localhost') . '/dashboard?subscription=success';
        $cancelUrl = config('app.url', 'http://localhost') . '/pricing?subscription=cancelled';

        $session = $this->stripeService->createCheckoutSession(
            $stripeCustomerId,
            $priceId,
            $successUrl,
            $cancelUrl
        );

        if (!$session) {
            return $this->jsonResponse($response, [
                'error' => true,
                'message' => 'Failed to create checkout session'
            ], 500);
        }

        return $this->jsonResponse($response, [
            'success' => true,
            'checkoutUrl' => $session->url,
            'sessionId' => $session->id
        ]);
    }

    public function cancel(Request $request, Response $response): Response
    {
        $userId = $request->getAttribute('userId');
        $user = $this->userModel->findById($userId);

        if (!$user) {
            return $this->jsonResponse($response, [
                'error' => true,
                'message' => 'User not found'
            ], 404);
        }

        $subscriptionId = $user['subscription']['stripeSubscriptionId'];

        if (!$subscriptionId) {
            return $this->jsonResponse($response, [
                'error' => true,
                'message' => 'No active subscription'
            ], 400);
        }

        // Cancel subscription at period end
        $subscription = $this->stripeService->cancelSubscription($subscriptionId);

        if (!$subscription) {
            return $this->jsonResponse($response, [
                'error' => true,
                'message' => 'Failed to cancel subscription'
            ], 500);
        }

        // Update subscription in DB
        $this->subscriptionModel->updateByStripeId($subscriptionId, [
            'cancelAtPeriodEnd' => true
        ]);

        return $this->jsonResponse($response, [
            'success' => true,
            'message' => 'Subscription will be cancelled at period end'
        ]);
    }

    public function update(Request $request, Response $response): Response
    {
        $userId = $request->getAttribute('userId');
        $data = $request->getParsedBody();
        $user = $this->userModel->findById($userId);

        if (!$user) {
            return $this->jsonResponse($response, [
                'error' => true,
                'message' => 'User not found'
            ], 404);
        }

        $subscriptionId = $user['subscription']['stripeSubscriptionId'];

        if (!$subscriptionId) {
            return $this->jsonResponse($response, [
                'error' => true,
                'message' => 'No active subscription'
            ], 400);
        }

        // Validate new plan
        $newPlan = $data['plan'] ?? '';
        if (!in_array($newPlan, ['starter', 'pro', 'enterprise'])) {
            return $this->jsonResponse($response, [
                'error' => true,
                'message' => 'Invalid plan'
            ], 400);
        }

        // Get new price ID
        $newPriceId = $this->stripeService->getPriceId($newPlan);

        if (!$newPriceId) {
            return $this->jsonResponse($response, [
                'error' => true,
                'message' => 'Price ID not configured for this plan'
            ], 500);
        }

        // Update subscription
        $subscription = $this->stripeService->updateSubscription($subscriptionId, $newPriceId);

        if (!$subscription) {
            return $this->jsonResponse($response, [
                'error' => true,
                'message' => 'Failed to update subscription'
            ], 500);
        }

        return $this->jsonResponse($response, [
            'success' => true,
            'message' => 'Subscription updated successfully'
        ]);
    }

    private function jsonResponse(Response $response, array $data, int $status = 200): Response
    {
        $response->getBody()->write(json_encode($data));
        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus($status);
    }
}
