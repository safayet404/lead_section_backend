<?php

return [
    'paths' => ['api/*', 'sanctum/csrf-cookie', 'login', 'logout', '*'],
    'allowed_methods' => ['*'],
    'allowed_origins' => [
        'http://localhost:5173',      // Vue dev server
        'http://127.0.0.1:5173',     // Alternative format
    ],
    'allowed_origins_patterns' => [],
    'allowed_headers' => ['*'],
    'exposed_headers' => ['Set-Cookie'],
    'max_age' => 0,
    'supports_credentials' => true,
];
