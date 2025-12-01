<?php
// Endpoint de callback para Google OAuth
require_once __DIR__ . '/../src/Database.php';
require_once __DIR__ . '/../src/Auth.php';
require_once __DIR__ . '/../src/User.php';
session_start();
$config = require __DIR__ . '/../config/config.php';
$auth = new Auth($config['google']);
$db = new Database($config['db']);
$userModel = new User($db);

if (!isset($_GET['code'])) {
    // iniciar login
    $url = $auth->getAuthUrl();
    header('Location: ' . $url);
    exit;
}

// intercambiar code por token y obtener perfil
$token = $auth->fetchAccessToken($_GET['code']);
$profile = $auth->getUserProfile($token['access_token'] ?? null);
if (!$profile) {
    die('No se pudo obtener perfil de Google');
}

// guardar/obtener usuario en DB
$existing = $userModel->getByGoogleId($profile['id']);
if ($existing) {
    $user = $existing;
} else {
    $user = $userModel->create($profile['email'], $profile['name'], $profile['id']);
}
$_SESSION['user_id'] = $user['id'];
header('Location: /');
exit;
