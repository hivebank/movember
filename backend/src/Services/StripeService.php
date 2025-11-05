<?php

namespace FormFlow\Services;

use Stripe\Stripe;
use Stripe\Customer;
use Stripe\Subscription as StripeSubscription;
use Stripe\Checkout\Session;
use Stripe\Webhook;

class StripeService
{
    private array $config;

    public function __construct()
    {
        $this->config = require __DIR__ . '/../../config/stripe.php';
        Stripe::setApiKey($this->config['secret_key']);
    }

    public function createCustomer(string $email, string $name): ?Customer
    {
        try {
            return Customer::create([
                'email' => $email,
                'name' => $name,
            ]);
        } catch (\Exception $e) {
            error_log("Stripe createCustomer error: " . $e->getMessage());
            return null;
        }
    }

    public function createCheckoutSession(
        string $customerId,
        string $priceId,
        string $successUrl,
        string $cancelUrl
    ): ?Session {
        try {
            return Session::create([
                'customer' => $customerId,
                'payment_method_types' => ['card'],
                'line_items' => [[
                    'price' => $priceId,
                    'quantity' => 1,
                ]],
                'mode' => 'subscription',
                'success_url' => $successUrl,
                'cancel_url' => $cancelUrl,
            ]);
        } catch (\Exception $e) {
            error_log("Stripe createCheckoutSession error: " . $e->getMessage());
            return null;
        }
    }

    public function createSubscription(string $customerId, string $priceId): ?StripeSubscription
    {
        try {
            return StripeSubscription::create([
                'customer' => $customerId,
                'items' => [['price' => $priceId]],
                'payment_behavior' => 'default_incomplete',
                'expand' => ['latest_invoice.payment_intent'],
            ]);
        } catch (\Exception $e) {
            error_log("Stripe createSubscription error: " . $e->getMessage());
            return null;
        }
    }

    public function cancelSubscription(string $subscriptionId): ?StripeSubscription
    {
        try {
            return StripeSubscription::update($subscriptionId, [
                'cancel_at_period_end' => true
            ]);
        } catch (\Exception $e) {
            error_log("Stripe cancelSubscription error: " . $e->getMessage());
            return null;
        }
    }

    public function updateSubscription(string $subscriptionId, string $newPriceId): ?StripeSubscription
    {
        try {
            $subscription = StripeSubscription::retrieve($subscriptionId);

            return StripeSubscription::update($subscriptionId, [
                'items' => [[
                    'id' => $subscription->items->data[0]->id,
                    'price' => $newPriceId,
                ]],
            ]);
        } catch (\Exception $e) {
            error_log("Stripe updateSubscription error: " . $e->getMessage());
            return null;
        }
    }

    public function getSubscription(string $subscriptionId): ?StripeSubscription
    {
        try {
            return StripeSubscription::retrieve($subscriptionId);
        } catch (\Exception $e) {
            error_log("Stripe getSubscription error: " . $e->getMessage());
            return null;
        }
    }

    public function constructWebhookEvent(string $payload, string $signature): ?\Stripe\Event
    {
        try {
            return Webhook::constructEvent(
                $payload,
                $signature,
                $this->config['webhook_secret']
            );
        } catch (\Exception $e) {
            error_log("Stripe webhook error: " . $e->getMessage());
            return null;
        }
    }

    public function getPlans(): array
    {
        return $this->config['plans'];
    }

    public function getPlanByName(string $planName): ?array
    {
        return $this->config['plans'][$planName] ?? null;
    }

    public function getPriceId(string $plan): ?string
    {
        return $this->config['prices'][$plan] ?? null;
    }
}
