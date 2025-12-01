<?php

/*
  Archivo: mini_library_project/mini_library_project/src/GoogleBooks.php
  Propósito:
    - Explica la responsabilidad principal de este archivo.
    - Describe las clases/funciones definidas aquí (si aplica).
    - Indica cómo interactúa con otras partes del proyecto.
    - Menciona requisitos previos (p. ej. dependencias, variables de configuración).
  Notas:
    - Mantén las credenciales fuera del código: usa config/config.php.
    - En producción, asegúrate de usar HTTPS y almacenamiento seguro para tokens.
*/

class GoogleBooks {
    private $apiBase = 'https://www.googleapis.com/books/v1/volumes';
    public function search($q, $maxResults = 12) {
        $params = http_build_query(['q' => $q, 'maxResults' => $maxResults]);
        $url = $this->apiBase . '?' . $params;
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $res = curl_exec($ch);
        curl_close($ch);
        $data = json_decode($res, true);
        return $data['items'] ?? [];
    }
    public function getById($googleId) {
        $url = $this->apiBase . '/' . urlencode($googleId);
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $res = curl_exec($ch);
        curl_close($ch);
        return json_decode($res, true);
    }
}
