<?php

/*
  Archivo: mini_library_project/mini_library_project/config/config.php
  Propósito:
    - Explica la responsabilidad principal de este archivo.
    - Describe las clases/funciones definidas aquí (si aplica).
    - Indica cómo interactúa con otras partes del proyecto.
    - Menciona requisitos previos (p. ej. dependencias, variables de configuración).
  Notas:
    - Mantén las credenciales fuera del código: usa config/config.php.
    - En producción, asegúrate de usar HTTPS y almacenamiento seguro para tokens.
*/

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
