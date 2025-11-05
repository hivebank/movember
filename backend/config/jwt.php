<?php

return [
    'secret' => $_ENV['JWT_SECRET'] ?? 'your-secret-key',
    'algorithm' => 'HS256',
    'expiration' => (int)($_ENV['JWT_EXPIRATION'] ?? 3600), // 1 hour
    'refresh_expiration' => (int)($_ENV['JWT_REFRESH_EXPIRATION'] ?? 2592000), // 30 days
];
