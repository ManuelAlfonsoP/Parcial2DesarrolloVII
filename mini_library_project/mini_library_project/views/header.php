<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Mi Biblioteca</title>
    <link rel="stylesheet" href="/assets/style.css">
</head>
<body>
<header>
    <h1>Mi Biblioteca Personal</h1>
    <nav>
        <a href="/">Inicio</a> |
        <a href="/?action=my_books">Mis libros</a> |
        <form style="display:inline" action="/" method="get">
            <input type="hidden" name="action" value="search">
            <input name="q" placeholder="Buscar libros..." required>
            <button>Buscar</button>
        </form>
        <?php if(isset($user) && $user): ?>
            <span> | Bienvenido <?=htmlspecialchars($user['nombre'])?></span>
            <a href="/public/logout.php">Cerrar sesión</a>
        <?php else: ?>
            <a href="/public/callback.php">Login con Google</a>
        <?php endif; ?>
    </nav>
</header>
<main>
