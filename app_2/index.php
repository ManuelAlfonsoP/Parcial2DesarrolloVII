<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once 'clases.php';

// (5) Crear el arreglo asociativo $estadosLegibles
$estadosLegibles = [
    'disponible' => 'DISPONIBLE',
    'prestado' => 'PRESTADO',
    'en_reparacion' => 'EN REPARACIÓN'
];

// Obtener la acción del query string, 'list' por defecto
$action = isset($_GET['action']) ? $_GET['action'] : 'list';

// Variables para ordenamiento y filtrado
$sortField = isset($_GET['field']) ? $_GET['field'] : 'id';
$sortDirection = isset($_GET['direction']) ? $_GET['direction'] : 'ASC';
$filterEstado = isset($_GET['filterEstado']) ? $_GET['filterEstado'] : '';

$gestorBiblioteca = new GestorBiblioteca();
$mensaje = null;
$recursoEnEdicion = null; // Variable para almacenar el recurso en edición

// (11) Función para manejo de errores básicos
function manejarError($mensaje) {
    global $mensaje;
    $mensaje = "Error: " . $mensaje;
}

// Procesar la acción
switch ($action) {
    case 'add':
    case 'edit':
        // (8) Implementar la lógica para agregar/actualizar recursos
        $datosComunes = ['titulo', 'autor', 'anioPublicacion', 'estado', 'fechaAdquisicion', 'tipo'];
        
        $datosValidos = true;
        foreach ($datosComunes as $campo) {
            if (!isset($_GET[$campo]) || empty($_GET[$campo])) {
                $datosValidos = false;
                break;
            }
        }

        if (!$datosValidos) {
            manejarError("Faltan campos comunes obligatorios.");
            $action = 'list'; // Volver a listar para mostrar el error
            break;
        }

        $datosRecurso = [
            'titulo' => $_GET['titulo'],
            'autor' => $_GET['autor'],
            'anioPublicacion' => intval($_GET['anioPublicacion']),
            'estado' => $_GET['estado'],
            'fechaAdquisicion' => $_GET['fechaAdquisicion'],
            'tipo' => $_GET['tipo'],
        ];

        $recurso = null;
        switch ($datosRecurso['tipo']) {
            case 'Libro':
                $isbn = $_GET['isbn'] ?? '';
                if (empty($isbn)) { manejarError("ISBN es obligatorio para un Libro."); break 2; }
                $datosRecurso['isbn'] = $isbn;
                $recurso = new Libro($datosRecurso);
                break;
            case 'Revista':
                $numeroEdicion = $_GET['numeroEdicion'] ?? '';
                if (!is_numeric($numeroEdicion)) { manejarError("Número de Edición inválido para Revista."); break 2; }
                $datosRecurso['numeroEdicion'] = intval($numeroEdicion);
                $recurso = new Revista($datosRecurso);
                break;
            case 'DVD':
                $duracion = $_GET['duracion'] ?? '';
                if (!is_numeric($duracion)) { manejarError("Duración inválida para DVD."); break 2; }
                $datosRecurso['duracion'] = intval($duracion);
                $recurso = new DVD($datosRecurso);
                break;
            default:
                manejarError("Tipo de recurso no reconocido.");
                break 2;
        }

        if ($action === 'add') {
            $gestorBiblioteca->agregarRecurso($recurso); // (8) Llamada a agregarRecurso
            $mensaje = "Recurso agregado exitosamente.";
        } elseif ($action === 'edit' && isset($_GET['id'])) {
            $recurso->id = intval($_GET['id']);
            $gestorBiblioteca->actualizarRecurso($recurso); // (8) Llamada a actualizarRecurso
            $mensaje = "Recurso actualizado exitosamente.";
        } else {
            manejarError("Acción de edición inválida: falta el ID.");
        }
        
        // Redireccionar para evitar reenvío del formulario (PRG pattern)
        header("Location: index.php?filterEstado=" . urlencode($filterEstado) . "&field=" . urlencode($sortField) . "&direction=" . urlencode($sortDirection));
        exit;
        // La acción se establecerá a 'list' después de la redirección o el break
        break;

    case 'edit_form': // Nueva acción para cargar el formulario de edición
        if (isset($_GET['id'])) {
            $recursoEnEdicion = $gestorBiblioteca->obtenerRecursoPorId($_GET['id']);
            if (!$recursoEnEdicion) {
                manejarError("Recurso no encontrado para editar.");
            }
        }
        $action = 'list'; // Continuar a la vista de listado/formulario
        break;

    case 'delete':
        if (isset($_GET['id'])) {
            $gestorBiblioteca->eliminarRecurso($_GET['id']); // (8) Llamada a eliminarRecurso
            $mensaje = "Recurso eliminado exitosamente.";
            header("Location: index.php?filterEstado=" . urlencode($filterEstado) . "&field=" . urlencode($sortField) . "&direction=" . urlencode($sortDirection));
            exit;
        } else {
            manejarError("ID de recurso no especificado para eliminar.");
        }
        break;

    case 'status':
        if (isset($_GET['id']) && isset($_GET['estado'])) {
            $id = intval($_GET['id']);
            $nuevoEstado = $_GET['estado'];
            if (array_key_exists($nuevoEstado, $estadosLegibles)) { // Validación básica del estado
                $gestorBiblioteca->actualizarEstadoRecurso($id, $nuevoEstado); // (8) Llamada a actualizarEstadoRecurso
                $mensaje = "Estado del recurso ID $id actualizado a '" . $estadosLegibles[$nuevoEstado] . "'.";
                header("Location: index.php?filterEstado=" . urlencode($filterEstado) . "&field=" . urlencode($sortField) . "&direction=" . urlencode($sortDirection));
                exit;
            } else {
                manejarError("Estado no válido.");
            }
        } else {
            manejarError("ID o estado no especificado para cambiar el estado.");
        }
        break;

    case 'filter':
        // La lógica del filtro es manejada por las variables $filterEstado
        if (isset($_GET['filterEstado'])) {
            $filterEstado = $_GET['filterEstado'];
        }
        // No se necesita lógica adicional aquí, se aplica en 'list'
        break;

    case 'sort':
        // La lógica del ordenamiento es manejada por las variables $sortField y $sortDirection
        if (isset($_GET['field']) && isset($_GET['direction'])) {
            $sortField = $_GET['field'];
            $sortDirection = $_GET['direction'];
        }
        // No se necesita lógica adicional aquí, se aplica en 'list'
        break;

    case 'list':
    default:
        // (8) Implementar la lógica para listar, filtrar y ordenar
        break;
}

// Cargar y listar los recursos con el filtro y ordenamiento aplicados
$recursos = $gestorBiblioteca->listarRecursos($filterEstado, $sortField, $sortDirection);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestor de Biblioteca</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1 class="mb-4">Gestor de Biblioteca</h1>
        
        <?php if (isset($mensaje)): ?>
            <div class="alert <?php echo strpos($mensaje, 'Error') === 0 ? 'alert-danger' : 'alert-success'; ?>" role="alert">
                <?php echo $mensaje; ?>
            </div>
        <?php endif; ?>

        <form action="index.php" method="GET" class="row g-3 mb-4 align-items-end">
            <input type="hidden" name="action" value="<?php echo $recursoEnEdicion ? 'edit' : 'add'; ?>">
            <?php if ($recursoEnEdicion): ?>
                <input type="hidden" name="id" value="<?php echo $recursoEnEdicion->id; ?>">
            <?php endif; ?>
            
            <div class="col-md-2">
                <input type="text" class="form-control" name="titulo" placeholder="Título" required
                       value="<?php echo $recursoEnEdicion ? htmlspecialchars($recursoEnEdicion->titulo) : ''; ?>">
            </div>
            <div class="col-md-2">
                <input type="text" class="form-control" name="autor" placeholder="Autor" required
                       value="<?php echo $recursoEnEdicion ? htmlspecialchars($recursoEnEdicion->autor) : ''; ?>">
            </div>
            <div class="col-md-1">
                <input type="number" class="form-control" name="anioPublicacion" placeholder="Año" required
                       value="<?php echo $recursoEnEdicion ? htmlspecialchars($recursoEnEdicion->anioPublicacion) : ''; ?>">
            </div>
            <div class="col-md-1">
                <select class="form-select" name="estado" required>
                    <option value="">Estado</option>
                    <?php foreach ($estadosLegibles as $valor => $texto): ?>
                        <option value="<?php echo $valor; ?>" <?php echo ($recursoEnEdicion && $recursoEnEdicion->estado == $valor) ? 'selected' : ''; ?>>
                            <?php echo $texto; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2">
                <input type="date" class="form-control" name="fechaAdquisicion" required
                       value="<?php echo $recursoEnEdicion ? htmlspecialchars($recursoEnEdicion->fechaAdquisicion) : ''; ?>">
            </div>
            <div class="col-md-2">
                <select class="form-select" name="tipo" required id="tipoRecurso">
                    <option value="">Tipo de Recurso</option>
                    <option value="Libro" <?php echo ($recursoEnEdicion && $recursoEnEdicion->tipo == 'Libro') ? 'selected' : ''; ?>>Libro</option>
                    <option value="Revista" <?php echo ($recursoEnEdicion && $recursoEnEdicion->tipo == 'Revista') ? 'selected' : ''; ?>>Revista</option>
                    <option value="DVD" <?php echo ($recursoEnEdicion && $recursoEnEdicion->tipo == 'DVD') ? 'selected' : ''; ?>>DVD</option>
                </select>
            </div>
            <?php 
                $isbn = ($recursoEnEdicion instanceof Libro) ? htmlspecialchars($recursoEnEdicion->isbn) : '';
                $numeroEdicion = ($recursoEnEdicion instanceof Revista) ? htmlspecialchars($recursoEnEdicion->numeroEdicion) : '';
                $duracion = ($recursoEnEdicion instanceof DVD) ? htmlspecialchars($recursoEnEdicion->duracion) : '';
            ?>
            <div class="col-md-2" id="columnaLibro" style="display:none;">
                <input type="text" class="form-control" id="campoLibro" name="isbn" placeholder="ISBN" value="<?php echo $isbn; ?>">
            </div>
            <div class="col-md-2" id="columnaRevista" style="display:none;">
                <input type="number" class="form-control" id="campoRevista" name="numeroEdicion" placeholder="Número de Edición" value="<?php echo $numeroEdicion; ?>">
            </div>
            <div class="col-md-2" id="columnaDVD" style="display:none;">
                <input type="number" class="form-control" id="campoDVD" name="duracion" placeholder="Duración (minutos)" value="<?php echo $duracion; ?>">
            </div>
            <div class="col-md-1">
                <button type="submit" class="btn btn-primary w-100">
                    <?php echo $recursoEnEdicion ? 'Actualizar' : 'Agregar'; ?>
                </button>
            </div>
            <?php if ($recursoEnEdicion): ?>
                <div class="col-md-1">
                    <a href="index.php" class="btn btn-secondary w-100">Cancelar</a>
                </div>
            <?php endif; ?>
        </form>

        <form action="index.php" method="GET" class="row g-3 mb-4 align-items-end">
            <input type="hidden" name="action" value="filter">
            <input type="hidden" name="field" value="<?php echo $sortField; ?>">
            <input type="hidden" name="direction" value="<?php echo $sortDirection; ?>">
            <div class="col-auto">
                <select name="filterEstado" class="form-select">
                    <option value="">Todos los estados</option>
                    <?php foreach ($estadosLegibles as $valor => $texto): ?>
                        <option value="<?php echo $valor; ?>" <?php echo $filterEstado == $valor ? 'selected' : ''; ?>><?php echo $texto; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-primary">Filtrar</button>
            </div>
        </form>

<table class="table table-striped table-hover">
            <thead>
                <tr>
                    <th><a href="index.php?action=sort&field=id&direction=<?php echo $sortField == 'id' && $sortDirection == 'ASC' ? 'DESC' : 'ASC'; ?>&filterEstado=<?php echo $filterEstado; ?>">ID <?php echo $sortField == 'id' ? ($sortDirection == 'ASC' ? '▲' : '▼') : ''; ?></a></th>
                    <th><a href="index.php?action=sort&field=titulo&direction=<?php echo $sortField == 'titulo' && $sortDirection == 'ASC' ? 'DESC' : 'ASC'; ?>&filterEstado=<?php echo $filterEstado; ?>">Título <?php echo $sortField == 'titulo' ? ($sortDirection == 'ASC' ? '▲' : '▼') : ''; ?></a></th>
                    <th><a href="index.php?action=sort&field=autor&direction=<?php echo $sortField == 'autor' && $sortDirection == 'ASC' ? 'DESC' : 'ASC'; ?>&filterEstado=<?php echo $filterEstado; ?>">Autor <?php echo $sortField == 'autor' ? ($sortDirection == 'ASC' ? '▲' : '▼') : ''; ?></a></th>
                    <th><a href="index.php?action=sort&field=anioPublicacion&direction=<?php echo $sortField == 'anioPublicacion' && $sortDirection == 'ASC' ? 'DESC' : 'ASC'; ?>&filterEstado=<?php echo $filterEstado; ?>">Año <?php echo $sortField == 'anioPublicacion' ? ($sortDirection == 'ASC' ? '▲' : '▼') : ''; ?></a></th>
                    <th><a href="index.php?action=sort&field=estado&direction=<?php echo $sortField == 'estado' && $sortDirection == 'ASC' ? 'DESC' : 'ASC'; ?>&filterEstado=<?php echo $filterEstado; ?>">Estado <?php echo $sortField == 'estado' ? ($sortDirection == 'ASC' ? '▲' : '▼') : ''; ?></a></th>
                    <th><a href="index.php?action=sort&field=fechaAdquisicion&direction=<?php echo $sortField == 'fechaAdquisicion' && $sortDirection == 'ASC' ? 'DESC' : 'ASC'; ?>&filterEstado=<?php echo $filterEstado; ?>">Fecha Adquisición <?php echo $sortField == 'fechaAdquisicion' ? ($sortDirection == 'ASC' ? '▲' : '▼') : ''; ?></a></th>
                    <th>Tipo</th>
                    <th>Detalles de Préstamo</th> 
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($recursos as $recurso): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($recurso->id); ?></td>
                        <td><?php echo htmlspecialchars($recurso->titulo); ?></td>
                        <td><?php echo htmlspecialchars($recurso->autor); ?></td>
                        <td><?php echo htmlspecialchars($recurso->anioPublicacion); ?></td>
                        <td><?php echo htmlspecialchars($estadosLegibles[$recurso->estado] ?? $recurso->estado); ?></td>
                        <td><?php echo htmlspecialchars($recurso->fechaAdquisicion); ?></td>
                        <td><?php echo htmlspecialchars($recurso->tipo); ?></td>
                        <td><?php echo htmlspecialchars($recurso->obtenerDetallesPrestamo()); ?></td> 
                        <td>
                            <a href='index.php?action=edit_form&id=<?php echo $recurso->id; ?>' class='btn btn-sm btn-warning'><i class='fas fa-edit'></i></a>
                            <a href='index.php?action=delete&id=<?php echo $recurso->id; ?>&filterEstado=<?php echo $filterEstado; ?>&field=<?php echo $sortField; ?>&direction=<?php echo $sortDirection; ?>' class='btn btn-sm btn-danger' onclick="return confirm('¿Está seguro de que desea eliminar este recurso?');"><i class='fas fa-trash'></i></a>
                            <select class='form-select form-select-sm d-inline-block w-auto' onchange="cambiarEstado(this, <?php echo $recurso->id; ?>, '<?php echo $filterEstado; ?>', '<?php echo $sortField; ?>', '<?php echo $sortDirection; ?>')">
                                <option value=''>Cambiar estado</option>
                                <?php foreach ($estadosLegibles as $valor => $texto): ?>
                                    <?php if ($recurso->estado !== $valor): ?>
                                        <option value='<?php echo $valor; ?>'><?php echo $texto; ?></option>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </select>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const tipoRecurso = document.getElementById('tipoRecurso');
            const columnaLibro = document.getElementById('columnaLibro');
            const columnaRevista = document.getElementById('columnaRevista');
            const columnaDVD = document.getElementById('columnaDVD');
            
            // (10) Función para actualizar la visibilidad y el atributo required de los campos específicos
            function actualizarCamposEspecificos() {
                const isLibro = tipoRecurso.value === 'Libro';
                const isRevista = tipoRecurso.value === 'Revista';
                const isDVD = tipoRecurso.value === 'DVD';

                columnaLibro.style.display = isLibro ? 'block' : 'none';
                columnaRevista.style.display = isRevista ? 'block' : 'none';
                columnaDVD.style.display = isDVD ? 'block' : 'none';
                
                // Opcionalmente, agregar o quitar el atributo 'required' (aunque el PHP lo valida)
                document.getElementById('campoLibro').required = isLibro;
                document.getElementById('campoRevista').required = isRevista;
                document.getElementById('campoDVD').required = isDVD;
            }

            tipoRecurso.addEventListener('change', actualizarCamposEspecificos);
            actualizarCamposEspecificos(); // Ejecutar al cargar para el modo edición
        });

        // Modificar la función para mantener el estado actual de orden y filtro
        function cambiarEstado(selectElement, recursoId, filterEstado, sortField, sortDirection) {
            if (selectElement.value) {
                window.location.href = 'index.php?action=status&id=' + recursoId + '&estado=' + selectElement.value +
                                        '&filterEstado=' + filterEstado + '&field=' + sortField + '&direction=' + sortDirection;
            }
        }        
    </script>
</body>
</html>