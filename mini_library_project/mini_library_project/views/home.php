<h2>Bienvenido a tu mini biblioteca</h2>
<p>Usa el buscador para encontrar libros vía Google Books y guárdalos en tu biblioteca personal.</p>
<?php if(!isset($user) || !$user): ?>
    <p><a href="/public/callback.php">Inicia sesión con Google para comenzar</a></p>
<?php else: ?>
    <p><a href="/?action=my_books">Ver mis libros</a></p>
<?php endif; ?>
