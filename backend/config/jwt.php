<?php

$mainConfig = require __DIR__ . '/config.php';

return [
    'secret' => $mainConfig['jwt']['secret'],
    'algorithm' => 'HS256',
    'expiration' => $mainConfig['jwt']['expiration'],
    'refresh_expiration' => $mainConfig['jwt']['refresh_expiration'],
];
