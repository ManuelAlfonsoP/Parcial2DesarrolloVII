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
