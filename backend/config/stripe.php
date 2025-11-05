<?php

return [
    'secret_key' => $_ENV['STRIPE_SECRET_KEY'] ?? '',
    'publishable_key' => $_ENV['STRIPE_PUBLISHABLE_KEY'] ?? '',
    'webhook_secret' => $_ENV['STRIPE_WEBHOOK_SECRET'] ?? '',
    'prices' => [
        'starter' => $_ENV['STRIPE_PRICE_STARTER'] ?? '',
        'pro' => $_ENV['STRIPE_PRICE_PRO'] ?? '',
        'enterprise' => $_ENV['STRIPE_PRICE_ENTERPRISE'] ?? '',
    ],
    'plans' => [
        'free' => [
            'name' => 'Free',
            'price' => 0,
            'forms_limit' => 3,
            'responses_limit' => 100,
            'features' => [
                'forms' => 3,
                'responses_per_month' => 100,
                'file_uploads' => false,
                'custom_branding' => false,
                'payment_collection' => false,
                'advanced_analytics' => false,
                'api_access' => false,
                'team_collaboration' => false,
            ]
        ],
        'starter' => [
            'name' => 'Starter',
            'price' => 19,
            'forms_limit' => 25,
            'responses_limit' => 1000,
            'features' => [
                'forms' => 25,
                'responses_per_month' => 1000,
                'file_uploads' => true,
                'custom_branding' => false,
                'payment_collection' => true,
                'advanced_analytics' => false,
                'api_access' => false,
                'team_collaboration' => false,
            ]
        ],
        'pro' => [
            'name' => 'Pro',
            'price' => 49,
            'forms_limit' => null, // unlimited
            'responses_limit' => 10000,
            'features' => [
                'forms' => 'unlimited',
                'responses_per_month' => 10000,
                'file_uploads' => true,
                'custom_branding' => true,
                'payment_collection' => true,
                'advanced_analytics' => true,
                'api_access' => true,
                'team_collaboration' => false,
            ]
        ],
        'enterprise' => [
            'name' => 'Enterprise',
            'price' => 199,
            'forms_limit' => null,
            'responses_limit' => null,
            'features' => [
                'forms' => 'unlimited',
                'responses_per_month' => 'unlimited',
                'file_uploads' => true,
                'custom_branding' => true,
                'payment_collection' => true,
                'advanced_analytics' => true,
                'api_access' => true,
                'team_collaboration' => true,
            ]
        ]
    ]
];
