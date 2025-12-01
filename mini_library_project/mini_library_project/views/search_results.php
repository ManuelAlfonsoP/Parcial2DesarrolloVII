<h2>Resultados de búsqueda</h2>
<?php if(empty($results)): ?>
    <p>No se encontraron resultados.</p>
<?php else: ?>
    <div class="grid">
    <?php foreach($results as $item): 
        $info = $item['volumeInfo'];
        $title = $info['title'] ?? '';
        $authors = isset($info['authors']) ? implode(', ', $info['authors']) : '';
        $thumb = $info['imageLinks']['thumbnail'] ?? '';
        $googleId = $item['id'];
    ?>
        <div class="card">
            <?php if($thumb): ?><img src="<?=htmlspecialchars($thumb)?>" alt="portada"><?php endif; ?>
            <h3><?=htmlspecialchars($title)?></h3>
            <p><?=htmlspecialchars($authors)?></p>
            <?php if(isset($user) && $user): ?>
                <form method="post" action="/?action=save">
                    <input type="hidden" name="google_id" value="<?=htmlspecialchars($googleId)?>">
                    <input type="hidden" name="title" value="<?=htmlspecialchars($title)?>">
                    <input type="hidden" name="authors" value="<?=htmlspecialchars($authors)?>">
                    <input type="hidden" name="thumbnail" value="<?=htmlspecialchars($thumb)?>">
                    <textarea name="review" placeholder="Tu reseña (opcional)"></textarea>
                    <button>Guardar en mi biblioteca</button>
                </form>
            <?php else: ?>
                <p><a href="/public/callback.php">Inicia sesión para guardar</a></p>
            <?php endif; ?>
        </div>
    <?php endforeach; ?>
    </div>
<?php endif; ?>
