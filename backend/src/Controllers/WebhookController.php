<?php

namespace FormFlow\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use FormFlow\Models\User;
use FormFlow\Models\Subscription;
use FormFlow\Services\StripeService;
use FormFlow\Services\EmailService;
use FormFlow\Database\MongoDB;

class WebhookController
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

    public function handleStripeWebhook(Request $request, Response $response): Response
    {
        $payload = (string)$request->getBody();
        $signature = $request->getHeaderLine('Stripe-Signature');

        // Verify webhook signature
        $event = $this->stripeService->constructWebhookEvent($payload, $signature);

        if (!$event) {
            return $this->jsonResponse($response, [
                'error' => true,
                'message' => 'Invalid signature'
            ], 400);
        }

        // Handle different event types
        switch ($event->type) {
            case 'checkout.session.completed':
                $this->handleCheckoutComplete($event->data->object);
                break;

            case 'customer.subscription.created':
            case 'customer.subscription.updated':
                $this->handleSubscriptionUpdate($event->data->object);
                break;

            case 'customer.subscription.deleted':
                $this->handleSubscriptionDeleted($event->data->object);
                break;

            case 'invoice.payment_succeeded':
                $this->handlePaymentSucceeded($event->data->object);
                break;

            case 'invoice.payment_failed':
                $this->handlePaymentFailed($event->data->object);
                break;
        }

        return $this->jsonResponse($response, [
            'success' => true,
            'message' => 'Webhook handled'
        ]);
    }

    private function handleCheckoutComplete($session): void
    {
        $customerId = $session->customer;
        $subscriptionId = $session->subscription;

        // Find user by Stripe customer ID
        $users = MongoDB::getInstance()->getCollection('users');
        $user = $users->findOne(['subscription.stripeCustomerId' => $customerId]);

        if (!$user) {
            error_log("User not found for Stripe customer: {$customerId}");
            return;
        }

        // Get subscription details
        $subscription = $this->stripeService->getSubscription($subscriptionId);

        if ($subscription) {
            $plan = $this->determinePlan($subscription->items->data[0]->price->id);

            // Update user subscription
            $this->userModel->updateSubscription((string)$user['_id'], [
                'plan' => $plan,
                'status' => 'active',
                'stripeCustomerId' => $customerId,
                'stripeSubscriptionId' => $subscriptionId,
                'currentPeriodEnd' => $subscription->current_period_end
            ]);

            // Create subscription record
            $this->subscriptionModel->create((string)$user['_id'], [
                'stripeSubscriptionId' => $subscriptionId,
                'stripePriceId' => $subscription->items->data[0]->price->id,
                'status' => 'active',
                'currentPeriodStart' => $subscription->current_period_start,
                'currentPeriodEnd' => $subscription->current_period_end,
                'cancelAtPeriodEnd' => false
            ]);

            // Send confirmation email
            $this->emailService->sendSubscriptionConfirmation($user['email'], ucfirst($plan));
        }
    }

    private function handleSubscriptionUpdate($subscription): void
    {
        $subscriptionId = $subscription->id;
        $plan = $this->determinePlan($subscription->items->data[0]->price->id);

        // Update subscription in DB
        $this->subscriptionModel->updateByStripeId($subscriptionId, [
            'status' => $subscription->status,
            'currentPeriodStart' => new \MongoDB\BSON\UTCDateTime($subscription->current_period_start * 1000),
            'currentPeriodEnd' => new \MongoDB\BSON\UTCDateTime($subscription->current_period_end * 1000),
            'cancelAtPeriodEnd' => $subscription->cancel_at_period_end
        ]);

        // Update user subscription
        $dbSubscription = $this->subscriptionModel->findByStripeSubscriptionId($subscriptionId);
        if ($dbSubscription) {
            $this->userModel->updateSubscription((string)$dbSubscription['userId'], [
                'plan' => $plan,
                'status' => $subscription->status,
                'stripeCustomerId' => $subscription->customer,
                'stripeSubscriptionId' => $subscriptionId,
                'currentPeriodEnd' => $subscription->current_period_end
            ]);
        }
    }

    private function handleSubscriptionDeleted($subscription): void
    {
        $subscriptionId = $subscription->id;

        // Update subscription in DB
        $this->subscriptionModel->updateByStripeId($subscriptionId, [
            'status' => 'cancelled'
        ]);

        // Downgrade user to free plan
        $dbSubscription = $this->subscriptionModel->findByStripeSubscriptionId($subscriptionId);
        if ($dbSubscription) {
            $this->userModel->updateSubscription((string)$dbSubscription['userId'], [
                'plan' => 'free',
                'status' => 'active',
                'stripeCustomerId' => $subscription->customer,
                'stripeSubscriptionId' => null,
                'currentPeriodEnd' => null
            ]);
        }
    }

    private function handlePaymentSucceeded($invoice): void
    {
        // Payment succeeded - log or send notification
        error_log("Payment succeeded for invoice: {$invoice->id}");
    }

    private function handlePaymentFailed($invoice): void
    {
        $subscriptionId = $invoice->subscription;

        // Update subscription status
        $this->subscriptionModel->updateByStripeId($subscriptionId, [
            'status' => 'past_due'
        ]);

        // Update user subscription
        $dbSubscription = $this->subscriptionModel->findByStripeSubscriptionId($subscriptionId);
        if ($dbSubscription) {
            $this->userModel->updateSubscription((string)$dbSubscription['userId'], [
                'plan' => 'free',
                'status' => 'past_due',
                'stripeCustomerId' => $invoice->customer,
                'stripeSubscriptionId' => $subscriptionId,
                'currentPeriodEnd' => null
            ]);
        }
    }

    private function determinePlan(string $priceId): string
    {
        $stripeConfig = require __DIR__ . '/../../config/stripe.php';

        foreach ($stripeConfig['prices'] as $plan => $configPriceId) {
            if ($configPriceId === $priceId) {
                return $plan;
            }
        }

        return 'free';
    }

    private function jsonResponse(Response $response, array $data, int $status = 200): Response
    {
        $response->getBody()->write(json_encode($data));
        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus($status);
    }
}
