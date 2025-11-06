<?php

/**
 * FormFlow Configuration
 *
 * This file contains all configuration settings with sensible defaults.
 * You can edit these values directly - no .env file needed!
 *
 * For production, make sure to:
 * 1. Change JWT_SECRET to a random string
 * 2. Set your Stripe keys
 * 3. Configure MongoDB with authentication if needed
 */

return [
    // Database Configuration
    'database' => [
        'uri' => 'mongodb://localhost:27017',
        'database' => 'formflow',
    ],

    // JWT Configuration
    'jwt' => [
        'secret' => 'formflow-secret-change-in-production-' . md5(__DIR__),
        'expiration' => 3600, // 1 hour
        'refresh_expiration' => 2592000, // 30 days
    ],

    // Stripe Configuration (optional - only needed for payments)
    'stripe' => [
        'secret_key' => '', // Add your Stripe secret key here
        'publishable_key' => '', // Add your Stripe publishable key here
        'webhook_secret' => '', // Add your Stripe webhook secret here

        // Stripe Price IDs for subscription plans
        'prices' => [
            'starter' => '', // Create in Stripe Dashboard
            'pro' => '',
            'enterprise' => '',
        ],
    ],

    // Email Configuration (optional - for notifications)
    'email' => [
        'smtp_host' => 'smtp.mailtrap.io',
        'smtp_port' => 2525,
        'smtp_user' => '',
        'smtp_password' => '',
        'smtp_from' => 'noreply@formflow.com',
    ],

    // Application Settings
    'app' => [
        'url' => 'http://localhost',
        'env' => 'production',
        'debug' => false,
    ],

    // CORS Settings
    'cors' => [
        'allowed_origins' => ['*'], // In production, specify your domain
    ],

    // Rate Limiting
    'rate_limit' => [
        'requests' => 100,
        'window' => 60, // seconds
    ],
];
