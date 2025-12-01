<?php

/*
  Archivo: mini_library_project/mini_library_project/src/Auth.php
  Propósito:
    - Explica la responsabilidad principal de este archivo.
    - Describe las clases/funciones definidas aquí (si aplica).
    - Indica cómo interactúa con otras partes del proyecto.
    - Menciona requisitos previos (p. ej. dependencias, variables de configuración).
  Notas:
    - Mantén las credenciales fuera del código: usa config/config.php.
    - En producción, asegúrate de usar HTTPS y almacenamiento seguro para tokens.
*/

class Auth {
    private $clientId;
    private $clientSecret;
    private $redirectUri;
    private $scope = 'openid email profile';
    public function __construct($cfg) {
        $this->clientId = $cfg['client_id'];
        $this->clientSecret = $cfg['client_secret'];
        $this->redirectUri = $cfg['redirect_uri'];
    }
    public function getAuthUrl() {
        $params = http_build_query([
            'client_id' => $this->clientId,
            'response_type' => 'code',
            'scope' => $this->scope,
            'redirect_uri' => $this->redirectUri,
            'access_type' => 'online',
            'prompt' => 'select_account consent',
        ]);
        return 'https://accounts.google.com/o/oauth2/v2/auth?' . $params;
    }
    public function fetchAccessToken($code) {
        $tokenUrl = 'https://oauth2.googleapis.com/token';
        $post = http_build_query([
            'code' => $code,
            'client_id' => $this->clientId,
            'client_secret' => $this->clientSecret,
            'redirect_uri' => $this->redirectUri,
            'grant_type' => 'authorization_code',
        ]);
        $ch = curl_init($tokenUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
        $res = curl_exec($ch);
        curl_close($ch);
        return json_decode($res, true);
    }
    public function getUserProfile($accessToken) {
        if (!$accessToken) return null;
        $ch = curl_init('https://www.googleapis.com/oauth2/v2/userinfo');
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Authorization: Bearer ' . $accessToken]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $res = curl_exec($ch);
        curl_close($ch);
        return json_decode($res, true);
    }
}
