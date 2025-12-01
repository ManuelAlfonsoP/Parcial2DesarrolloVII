<?php
/*
  Archivo: mini_library_project/mini_library_project/views/my_books.php
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
<h2>Mis libros guardados</h2>
<?php if(empty($myBooks)): ?>
    <p>No has guardado libros aún.</p>
<?php else: ?>
    <ul class="saved-list">
    <?php foreach($myBooks as $b): ?>
        <li>
            <?php if($b['imagen_portada']): ?><img src="<?=htmlspecialchars($b['imagen_portada'])?>" alt="" style="height:80px"><?php endif; ?>
            <strong><?=htmlspecialchars($b['titulo'])?></strong> — <?=htmlspecialchars($b['autor'])?><br>
            <em><?=htmlspecialchars($b['reseña_personal'])?></em><br>
            <a href="/?action=delete&id=<?=urlencode($b['id'])?>" onclick="return confirm('Eliminar?')">Eliminar</a>
        </li>
    <?php endforeach; ?>
    </ul>
<?php endif; ?>
