<?php

/*
  Archivo: mini_library_project/mini_library_project/public/index.php
  Propósito:
    - Explica la responsabilidad principal de este archivo.
    - Describe las clases/funciones definidas aquí (si aplica).
    - Indica cómo interactúa con otras partes del proyecto.
    - Menciona requisitos previos (p. ej. dependencias, variables de configuración).
  Notas:
    - Mantén las credenciales fuera del código: usa config/config.php.
    - En producción, asegúrate de usar HTTPS y almacenamiento seguro para tokens.
*/

require_once __DIR__ . '/../src/Database.php';
require_once __DIR__ . '/../src/Auth.php';
require_once __DIR__ . '/../src/GoogleBooks.php';
require_once __DIR__ . '/../src/User.php';
require_once __DIR__ . '/../src/Book.php';

session_start();
$config = require __DIR__ . '/../config/config.php';

$auth = new Auth($config['google']);
$db = new Database($config['db']);
$googleBooks = new GoogleBooks();

$user = null;
if (isset($_SESSION['user_id'])) {
    $userModel = new User($db);
    $user = $userModel->getById($_SESSION['user_id']);
}

$action = $_GET['action'] ?? null;

if ($action === 'search' && !empty($_GET['q'])) {
    $q = trim($_GET['q']);
    $results = $googleBooks->search($q);
    include __DIR__ . '/../views/header.php';
    include __DIR__ . '/../views/search_results.php';
    include __DIR__ . '/../views/footer.php';
    exit;
}

if ($action === 'save' && $_SERVER['REQUEST_METHOD'] === 'POST' && $user) {
    $book = new Book($db);
    $google_id = $_POST['google_id'] ?? '';
    $title = $_POST['title'] ?? '';
    $authors = $_POST['authors'] ?? '';
    $thumbnail = $_POST['thumbnail'] ?? '';
    $review = $_POST['review'] ?? '';
    $book->saveForUser($user['id'], $google_id, $title, $authors, $thumbnail, $review);
    header('Location: /?action=my_books');
    exit;
}

if ($action === 'my_books') {
    if (!$user) {
        header('Location: /');
        exit;
    }
    $book = new Book($db);
    $myBooks = $book->getByUser($user['id']);
    include __DIR__ . '/../views/header.php';
    include __DIR__ . '/../views/my_books.php';
    include __DIR__ . '/../views/footer.php';
    exit;
}

if ($action === 'delete' && isset($_GET['id']) && $user) {
    $book = new Book($db);
    $book->deleteByIdAndUser($_GET['id'], $user['id']);
    header('Location: /?action=my_books');
    exit;
}

include __DIR__ . '/../views/header.php';
include __DIR__ . '/../views/home.php';
include __DIR__ . '/../views/footer.php';
