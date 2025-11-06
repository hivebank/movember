<?php
// Test script to debug login issues
// Visit: http://yourserver.com/test-login.php

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>FormFlow Login Troubleshoot</h1>";
echo "<hr>";

// Test 1: Check backend API is accessible
echo "<h2>Test 1: Backend API Accessibility</h2>";
$apiUrl = 'backend/public/api/health';
$ch = curl_init($apiUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 5);
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode === 200) {
    echo "✅ Backend API is accessible<br>";
    echo "Response: " . htmlspecialchars($response) . "<br>";
} else {
    echo "❌ Backend API NOT accessible (HTTP $httpCode)<br>";
    echo "URL tested: $apiUrl<br>";
    echo "Response: " . htmlspecialchars($response) . "<br>";
}

echo "<hr>";

// Test 2: Try to login
echo "<h2>Test 2: Login Test</h2>";
$loginUrl = 'backend/public/api/auth/login';
$loginData = json_encode([
    'email' => 'admin@admin.com',
    'password' => 'admin'
]);

$ch = curl_init($loginUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $loginData);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
curl_setopt($ch, CURLOPT_TIMEOUT, 5);
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curlError = curl_error($ch);
curl_close($ch);

echo "URL: $loginUrl<br>";
echo "HTTP Code: $httpCode<br>";
echo "Response: <pre>" . htmlspecialchars($response) . "</pre><br>";

if ($curlError) {
    echo "❌ cURL Error: $curlError<br>";
}

if ($httpCode === 200) {
    echo "✅ Login API responded successfully<br>";
} else {
    echo "❌ Login failed<br>";
}

echo "<hr>";

// Test 3: MongoDB connection via backend
echo "<h2>Test 3: MongoDB Status</h2>";
echo "Check if MongoDB is running:<br>";
echo "<code>sudo systemctl status mongod</code><br>";
echo "or<br>";
echo "<code>mongosh --eval 'db.runCommand({ping:1})'</code><br>";

echo "<hr>";

// Test 4: Check if admin user exists
echo "<h2>Test 4: Check Admin User</h2>";
echo "Run this command to check if admin exists:<br>";
echo "<code>mongosh formflow --eval \"db.users.findOne({email: 'admin@admin.com'})\"</code><br>";

echo "<hr>";
echo "<h2>Quick Fixes:</h2>";
echo "<ol>";
echo "<li><strong>If backend API not accessible:</strong> Check Apache configuration points to this directory</li>";
echo "<li><strong>If MongoDB not running:</strong> Start it with <code>sudo systemctl start mongod</code></li>";
echo "<li><strong>If admin user doesn't exist:</strong> Run <code>mongosh formflow < create-admin-user.js</code></li>";
echo "<li><strong>If still failing:</strong> Check backend/.env file has correct MongoDB settings</li>";
echo "</ol>";
?>
