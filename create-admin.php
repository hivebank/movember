// Create admin user for testing
// Run: php create-admin.php

<?php
require __DIR__ . '/backend/vendor/autoload.php';

use MongoDB\Client;

// Load environment
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/backend');
$dotenv->safeLoad();

$mongoUri = $_ENV['MONGODB_URI'] ?? 'mongodb://localhost:27017';
$mongoDb = $_ENV['MONGODB_DATABASE'] ?? 'formflow';

$client = new Client($mongoUri);
$db = $client->selectDatabase($mongoDb);
$users = $db->selectCollection('users');

// Check if admin exists
$existingAdmin = $users->findOne(['email' => 'admin@admin.com']);

if ($existingAdmin) {
    echo "Admin user already exists!\n";
    echo "Email: admin@admin.com\n";
    echo "Password: admin\n";
    exit;
}

// Create admin user
$admin = [
    'email' => 'admin@admin.com',
    'password' => password_hash('admin', PASSWORD_BCRYPT),
    'name' => 'Admin User',
    'company' => 'FormFlow',
    'subscription' => [
        'plan' => 'enterprise',
        'status' => 'active',
        'stripeCustomerId' => null,
        'stripeSubscriptionId' => null,
        'currentPeriodEnd' => null
    ],
    'createdAt' => new MongoDB\BSON\UTCDateTime(),
    'updatedAt' => new MongoDB\BSON\UTCDateTime()
];

$result = $users->insertOne($admin);

if ($result->getInsertedCount() > 0) {
    echo "✅ Admin user created successfully!\n\n";
    echo "Login credentials:\n";
    echo "Email: admin@admin.com\n";
    echo "Password: admin\n\n";
    echo "Visit your site and login with these credentials.\n";
} else {
    echo "❌ Failed to create admin user\n";
}
