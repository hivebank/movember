<?php

$mainConfig = require __DIR__ . '/config.php';

return [
    'mongodb' => [
        'uri' => $mainConfig['database']['uri'],
        'database' => $mainConfig['database']['database'],
        'options' => [
            'connectTimeoutMS' => 5000,
            'serverSelectionTimeoutMS' => 5000,
        ]
    ]
];
