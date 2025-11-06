<?php
session_start();

// API Base URL
define('API_URL', 'backend/public/api');

// Helper function to make API calls
function apiCall($endpoint, $method = 'GET', $data = null) {
    $url = API_URL . $endpoint;

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);

    $headers = ['Content-Type: application/json'];

    // Add auth token if logged in
    if (isset($_SESSION['token'])) {
        $headers[] = 'Authorization: Bearer ' . $_SESSION['token'];
    }

    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

    if ($data && in_array($method, ['POST', 'PUT', 'PATCH'])) {
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    }

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    return [
        'code' => $httpCode,
        'data' => json_decode($response, true)
    ];
}

// Check if user is logged in
function isLoggedIn() {
    return isset($_SESSION['user_id']) && isset($_SESSION['token']);
}

// Redirect if not logged in
function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: /login.php');
        exit;
    }
}
