<?php

return [
    'mongodb' => [
        'uri' => $_ENV['MONGODB_URI'] ?? 'mongodb://localhost:27017',
        'database' => $_ENV['MONGODB_DATABASE'] ?? 'formflow',
        'options' => [
            'connectTimeoutMS' => 5000,
            'serverSelectionTimeoutMS' => 5000,
        ]
    ]
];
