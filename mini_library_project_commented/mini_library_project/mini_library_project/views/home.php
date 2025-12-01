<?php
/*
  Archivo: mini_library_project/mini_library_project/views/home.php
  Propósito:
    - Explica la responsabilidad principal de este archivo.
    - Describe las clases/funciones definidas aquí (si aplica).
    - Indica cómo interactúa con otras partes del proyecto.
    - Menciona requisitos previos (p. ej. dependencias, variables de configuración).
  Notas:
    - Mantén las credenciales fuera del código: usa config/config.php.
    - En producción, asegúrate de usar HTTPS y almacenamiento seguro para tokens.
*/

?>
<h2>Bienvenido a tu mini biblioteca</h2>
<p>Usa el buscador para encontrar libros vía Google Books y guárdalos en tu biblioteca personal.</p>
<?php if(!isset($user) || !$user): ?>
    <p><a href="/public/callback.php">Inicia sesión con Google para comenzar</a></p>
<?php else: ?>
    <p><a href="/?action=my_books">Ver mis libros</a></p>
<?php endif; ?>
