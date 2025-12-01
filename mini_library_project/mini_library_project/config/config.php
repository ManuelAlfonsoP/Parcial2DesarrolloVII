<?php
// CONFIG - rellenar con tus datos antes de usar
return [
    'db' => [
        'host' => '127.0.0.1',
        'dbname' => 'mini_library',
        'user' => 'root',
        'pass' => '',
        'charset' => 'utf8mb4',
    ],
    'google' => [
        'client_id' => 'TU_GOOGLE_CLIENT_ID',
        'client_secret' => 'TU_GOOGLE_CLIENT_SECRET',
        'redirect_uri' => 'http://localhost:8000/callback.php',
    ],
];
