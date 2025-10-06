<?php
$archivoRegistros = 'registros.json';
$registros = [];

if (file_exists($archivoRegistros)) {
    $contenido = file_get_contents($archivoRegistros);
    $registros = json_decode($contenido, true) ?? [];
}
?>

<h2>Resumen de Registros</h2>

<?php if (empty($registros)): ?>
    <p>No hay registros todavía.</p>
<?php else: ?>
    <table>
        <thead>
            <tr>
                <th>Nombre</th>
                <th>Edad</th>
                <th>Fecha de Nacimiento</th>
                <th>Email</th>
                <th>Sitio Web</th>
                <th>Género</th>
                <th>Intereses</th>
                <th>Comentarios</th>
                <th>Foto de Perfil</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($registros as $registro): ?>
                <tr>
                    <td><?php echo htmlspecialchars($registro['nombre']); ?></td>
                    <td><?php echo htmlspecialchars($registro['edad']); ?></td>
                    <td><?php echo htmlspecialchars($registro['fechanacimiento']); ?></td>
                    <td><?php echo htmlspecialchars($registro['email']); ?></td>
                    <td><a href="<?php echo htmlspecialchars($registro['sitioWeb']); ?>" target="_blank"><?php echo htmlspecialchars($registro['sitioWeb']); ?></a></td>
                    <td><?php echo htmlspecialchars($registro['genero']); ?></td>
                    <td><?php echo isset($registro['intereses']) ? implode(", ", $registro['intereses']) : ''; ?></td>
                    <td><?php echo htmlspecialchars($registro['comentarios']); ?></td>
                    <td>
                        <?php if (isset($registro['foto_perfil'])): ?>
                            <img src="<?php echo htmlspecialchars($registro['foto_perfil']); ?>" width="100">
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>