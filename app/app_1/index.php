<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once 'clases.php';

// --- Arreglos de Traducciones ---
// 9. Arreglo para estados legibles
$estadosLegibles = [
    'pendiente' => 'Pendiente',
    'en_progreso' => 'En Progreso',
    'completada' => 'Completada'
];

// 10. Arreglo para prioridades legibles
$prioridadesLegibles = [
    1 => 'Alta',
    2 => 'Media alta',
    3 => 'Media',
    4 => 'Media baja',
    5 => 'Baja'
];
// --- Fin Arreglos de Traducciones ---


// Inicializar Gestor de Tareas
$gestorTareas = new GestorTareas();
$mensaje = null;

// Obtener la acción del query string, 'list' por defecto
$action = isset($_GET['action']) ? $_GET['action'] : 'list';

// Variable para almacenar la tarea en edición
$tareaEnEdicion = null;

// Variables para ordenamiento y filtrado
$sortField = isset($_GET['field']) ? $_GET['field'] : 'id';
$sortDirection = isset($_GET['direction']) ? $_GET['direction'] : 'ASC';
$filterEstado = isset($_GET['filterEstado']) ? $_GET['filterEstado'] : '';

$tareas = [];


// 7. Procesar la acción
switch ($action) {
    case 'add':
        if (isset($_GET['titulo'], $_GET['descripcion'], $_GET['prioridad'], $_GET['tipo'])) {
            // Recolectar datos y campos específicos
            $datos = [
                'titulo' => $_GET['titulo'],
                'descripcion' => $_GET['descripcion'],
                'prioridad' => (int) $_GET['prioridad'],
                'tipo' => $_GET['tipo'],
            ];

            // Añadir campos específicos si existen
            if ($datos['tipo'] === 'desarrollo' && isset($_GET['lenguajeProgramacion'])) {
                $datos['lenguajeProgramacion'] = $_GET['lenguajeProgramacion'];
            } elseif ($datos['tipo'] === 'diseno' && isset($_GET['herramientaDiseno'])) {
                $datos['herramientaDiseno'] = $_GET['herramientaDiseno'];
            } elseif ($datos['tipo'] === 'testing' && isset($_GET['tipoTest'])) {
                $datos['tipoTest'] = $_GET['tipoTest'];
            }

            $gestorTareas->agregarTarea($datos);
            $mensaje = "Tarea agregada con éxito.";
        } else {
             $mensaje = "Error: Faltan parámetros para agregar tarea.";
        }
        // Después de la acción, redirigir a list para evitar reenvío del form
        // header('Location: index.php'); exit;
        break;

    case 'edit':
        if (isset($_GET['id'], $_GET['titulo'], $_GET['descripcion'], $_GET['prioridad'], $_GET['tipo'])) {
            $datos = [
                'id' => (int) $_GET['id'],
                'titulo' => $_GET['titulo'],
                'descripcion' => $_GET['descripcion'],
                'prioridad' => (int) $_GET['prioridad'],
                'tipo' => $_GET['tipo'],
            ];

            // Añadir campos específicos si existen
            if ($datos['tipo'] === 'desarrollo' && isset($_GET['lenguajeProgramacion'])) {
                $datos['lenguajeProgramacion'] = $_GET['lenguajeProgramacion'];
            } elseif ($datos['tipo'] === 'diseno' && isset($_GET['herramientaDiseno'])) {
                $datos['herramientaDiseno'] = $_GET['herramientaDiseno'];
            } elseif ($datos['tipo'] === 'testing' && isset($_GET['tipoTest'])) {
                $datos['tipoTest'] = $_GET['tipoTest'];
            }
            
            if ($gestorTareas->actualizarTarea($datos)) {
                $mensaje = "Tarea actualizada con éxito.";
            } else {
                 $mensaje = "Error: No se pudo encontrar la tarea para actualizar.";
            }
        } elseif (isset($_GET['id']) && !isset($_GET['titulo'])) {
            // Acción de PRE-EDICIÓN: Cargar la tarea en el formulario
            $tareaEnEdicion = $gestorTareas->obtenerTareaPorId($_GET['id']);
            if (!$tareaEnEdicion) {
                $mensaje = "Error: Tarea no encontrada.";
            }
        } else {
             $mensaje = "Error: Faltan parámetros para editar tarea.";
        }
        break;

    case 'delete':
        if (isset($_GET['id'])) {
            $gestorTareas->eliminarTarea($_GET['id']);
            $mensaje = "Tarea eliminada con éxito.";
        } else {
            $mensaje = "Error: Falta el ID para eliminar la tarea.";
        }
        break;

    case 'status':
        if (isset($_GET['id']) && isset($_GET['estado'])) {
            if ($gestorTareas->actualizarEstadoTarea($_GET['id'], $_GET['estado'])) {
                $mensaje = "Estado de la tarea actualizado.";
            } else {
                $mensaje = "Error: Estado de la tarea no válido o ID no encontrado.";
            }
        } else {
            $mensaje = "Error: Faltan parámetros para actualizar el estado.";
        }
        break;

    case 'sort':
        // No se necesita lógica aquí, el listado se encarga de sortField y sortDirection
        break;
        
    case 'filter':
        // No se necesita lógica aquí, el listado se encarga de filterEstado
        break;

    case 'list':
    default:
        // No se necesita lógica específica para el caso por defecto
        break;
}


// Cargar las tareas, aplicando filtro y ordenamiento si existen
$tareas = $gestorTareas->listarTareas($filterEstado, $sortField, $sortDirection);

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestor de Tareas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1 class="mb-4">Gestor de Tareas</h1>
        
        <?php if (isset($mensaje)): ?>
            <div class="alert alert-info" role="alert">
                <?php echo $mensaje; ?>
            </div>
        <?php endif; ?>

        <form action="index.php" method="GET" class="row g-3 mb-4 align-items-end">
            <input type="hidden" name="action" value="<?php echo $tareaEnEdicion ? 'edit' : 'add'; ?>">
            <?php if ($tareaEnEdicion): ?>
                <input type="hidden" name="id" value="<?php echo $tareaEnEdicion->id; ?>">
            <?php endif; ?>
            
            <div class="col-lg-2 col-md-4 col-sm-6">
                <input type="text" class="form-control" name="titulo" placeholder="Título" required
                       value="<?php echo $tareaEnEdicion ? htmlspecialchars($tareaEnEdicion->titulo) : ''; ?>">
            </div>
            <div class="col-lg-2 col-md-4 col-sm-6">
                <input type="text" class="form-control" name="descripcion" placeholder="Descripción" required
                       value="<?php echo $tareaEnEdicion ? htmlspecialchars($tareaEnEdicion->descripcion) : ''; ?>">
            </div>
            <div class="col-lg-2 col-md-4 col-sm-6">
                <select class="form-select" name="prioridad" required>
                    <option value="">Prioridad</option>
                    <?php
                    // Usar $prioridadesLegibles (10)
                    foreach ($prioridadesLegibles as $value => $label) {
                        $selected = ($tareaEnEdicion && $tareaEnEdicion->prioridad == $value) ? 'selected' : '';
                        echo "<option value=\"$value\" $selected>$value ($label)</option>";
                    }
                    ?>
                </select>
            </div>
            <div class="col-lg-2 col-md-4 col-sm-6">
                <select class="form-select" name="tipo" required id="tipoTarea">
                    <option value="">Tipo de Tarea</option>
                    <option value="desarrollo" <?php echo ($tareaEnEdicion && $tareaEnEdicion->tipo == 'desarrollo') ? 'selected' : ''; ?>>Desarrollo</option>
                    <option value="diseno" <?php echo ($tareaEnEdicion && $tareaEnEdicion->tipo == 'diseno') ? 'selected' : ''; ?>>Diseño</option>
                    <option value="testing" <?php echo ($tareaEnEdicion && $tareaEnEdicion->tipo == 'testing') ? 'selected' : ''; ?>>Testing</option>
                </select>
            </div>
            <?php 
                $displayStyle = ($tareaEnEdicion && in_array($tareaEnEdicion->tipo, ['desarrollo', 'diseno', 'testing'])) ? 'block' : 'none';
                $isDesarrollo = ($tareaEnEdicion && $tareaEnEdicion->tipo == 'desarrollo');
                $isDiseno = ($tareaEnEdicion && $tareaEnEdicion->tipo == 'diseno');
                $isTesting = ($tareaEnEdicion && $tareaEnEdicion->tipo == 'testing');
            ?>
            <div class="col-lg-2 col-md-4 col-sm-6" id="campoEspecifico" style="display:<?php echo $displayStyle; ?>;">
                <input type="text" class="form-control" id="campoDesarrollo" name="lenguajeProgramacion" placeholder="Lenguaje de Programación" 
                       value="<?php echo $isDesarrollo ? htmlspecialchars($tareaEnEdicion->lenguajeProgramacion ?? '') : ''; ?>"
                       style="display:<?php echo $isDesarrollo ? 'block' : 'none'; ?>;">
                <input type="text" class="form-control" id="campoDiseno" name="herramientaDiseno" placeholder="Herramienta de Diseño" 
                       value="<?php echo $isDiseno ? htmlspecialchars($tareaEnEdicion->herramientaDiseno ?? '') : ''; ?>"
                       style="display:<?php echo $isDiseno ? 'block' : 'none'; ?>;">
                <select class="form-select" id="campoTesting" name="tipoTest" style="display:<?php echo $isTesting ? 'block' : 'none'; ?>;">
                    <option value="">Tipo de Test</option>
                    <option value="unitario" <?php echo $isTesting && $tareaEnEdicion->tipoTest == 'unitario' ? 'selected' : ''; ?>>Unitario</option>
                    <option value="integracion" <?php echo $isTesting && $tareaEnEdicion->tipoTest == 'integracion' ? 'selected' : ''; ?>>Integración</option>
                    <option value="e2e" <?php echo $isTesting && $tareaEnEdicion->tipoTest == 'e2e' ? 'selected' : ''; ?>>E2E</option>
                </select>
            </div>
            <div class="col-lg-2 col-md-4 col-sm-6">
                <button type="submit" class="btn btn-primary w-100">
                    <?php echo $tareaEnEdicion ? 'Actualizar Tarea' : 'Agregar Tarea'; ?>
                </button>
            </div>
            <?php if ($tareaEnEdicion): ?>
                <div class="col-lg-12 col-md-12 col-sm-12 text-end">
                    <a href="index.php" class="btn btn-secondary">Cancelar Edición</a>
                </div>
            <?php endif; ?>
        </form>

        <form action="index.php" method="GET" class="row g-3 mb-4 align-items-end">
            <input type="hidden" name="action" value="filter">
            <div class="col-auto">
                <select name="filterEstado" class="form-select">
                    <option value="">Todos los estados</option>
                    <?php foreach ($estadosLegibles as $value => $label): ?>
                        <option value="<?php echo $value; ?>" <?php echo $filterEstado == $value ? 'selected' : ''; ?>>
                            <?php echo $label; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-info">Filtrar</button>
            </div>
            <div class="col-auto">
                <a href="index.php" class="btn btn-secondary">Limpiar Filtro</a>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th><a href="index.php?action=sort&field=id&direction=<?php echo $sortField == 'id' && $sortDirection == 'ASC' ? 'DESC' : 'ASC'; ?>">ID <?php echo $sortField == 'id' ? ($sortDirection == 'ASC' ? '▲' : '▼') : ''; ?></a></th>
                        <th><a href="index.php?action=sort&field=titulo&direction=<?php echo $sortField == 'titulo' && $sortDirection == 'ASC' ? 'DESC' : 'ASC'; ?>">Título <?php echo $sortField == 'titulo' ? ($sortDirection == 'ASC' ? '▲' : '▼') : ''; ?></a></th>
                        <th>Descripción</th>
                        <th><a href="index.php?action=sort&field=estado&direction=<?php echo $sortField == 'estado' && $sortDirection == 'ASC' ? 'DESC' : 'ASC'; ?>">Estado <?php echo $sortField == 'estado' ? ($sortDirection == 'ASC' ? '▲' : '▼') : ''; ?></a></th>
                        <th><a href="index.php?action=sort&field=prioridad&direction=<?php echo $sortField == 'prioridad' && $sortDirection == 'ASC' ? 'DESC' : 'ASC'; ?>">Prioridad <?php echo $sortField == 'prioridad' ? ($sortDirection == 'ASC' ? '▲' : '▼') : ''; ?></a></th>
                        <th>Tipo</th>
                        <th>Detalles Específicos</th>
                        <th><a href="index.php?action=sort&field=fechaCreacion&direction=<?php echo $sortField == 'fechaCreacion' && $sortDirection == 'ASC' ? 'DESC' : 'ASC'; ?>">Fecha Creación <?php echo $sortField == 'fechaCreacion' ? ($sortDirection == 'ASC' ? '▲' : '▼') : ''; ?></a></th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($tareas as $tarea): ?>
                        <tr>
                            <td><?php echo $tarea->id; ?></td>
                            <td><?php echo htmlspecialchars($tarea->titulo); ?></td>
                            <td><?php echo htmlspecialchars($tarea->descripcion); ?></td>
                            <td><?php echo $estadosLegibles[$tarea->estado] ?? $tarea->estado; ?></td>
                            <td><?php echo $prioridadesLegibles[$tarea->prioridad] ?? $tarea->prioridad; ?></td>
                            <td><?php echo htmlspecialchars(ucfirst($tarea->tipo)); ?></td>
                            <td><?php echo htmlspecialchars($tarea->obtenerDetallesEspecificos()); ?></td>
                            <td><?php echo $tarea->fechaCreacion; ?></td>
                            <td>
                                <a href='index.php?action=edit&id=<?php echo $tarea->id; ?>' class='btn btn-sm btn-warning' title="Editar"><i class='fas fa-edit'></i></a>
                                <a href='index.php?action=delete&id=<?php echo $tarea->id; ?>' class='btn btn-sm btn-danger' onclick="return confirm('¿Está seguro de que desea eliminar esta tarea?');" title="Eliminar"><i class='fas fa-trash'></i></a>
                                <div class='btn-group'>
                                    <button type='button' class='btn btn-sm btn-secondary dropdown-toggle' data-bs-toggle='dropdown' title="Cambiar Estado">
                                        Estado
                                    </button>
                                    <ul class='dropdown-menu'>
                                        <?php foreach ($estadosLegibles as $value => $label): ?>
                                            <li><a class='dropdown-item' href='index.php?action=status&id=<?php echo $tarea->id; ?>&estado=<?php echo $value; ?>'><?php echo $label; ?></a></li>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    // 11. Script para mostrar/ocultar campos específicos
    document.addEventListener('DOMContentLoaded', function() {
        const tipoTareaSelect = document.getElementById('tipoTarea');
        const campoEspecifico = document.getElementById('campoEspecifico');
        const campoDesarrollo = document.getElementById('campoDesarrollo');
        const campoDiseno = document.getElementById('campoDiseno');
        const campoTesting = document.getElementById('campoTesting');
        
        function actualizarCamposEspecificos() {
            const selectedType = tipoTareaSelect.value;
            
            campoEspecifico.style.display = 'none';
            campoDesarrollo.style.display = 'none';
            campoDiseno.style.display = 'none';
            campoTesting.style.display = 'none';
            
            // Ocultar todos los campos específicos y eliminar el atributo 'required'
            campoDesarrollo.removeAttribute('required');
            campoDiseno.removeAttribute('required');
            campoTesting.removeAttribute('required');

            switch(selectedType) {
                case 'desarrollo':
                    campoEspecifico.style.display = 'block';
                    campoDesarrollo.style.display = 'block';
                    campoDesarrollo.setAttribute('required', 'required');
                    break;
                case 'diseno':
                    campoEspecifico.style.display = 'block';
                    campoDiseno.style.display = 'block';
                    campoDiseno.setAttribute('required', 'required');
                    break;
                case 'testing':
                    campoEspecifico.style.display = 'block';
                    campoTesting.style.display = 'block';
                    campoTesting.setAttribute('required', 'required');
                    break;
            }
        }

        tipoTareaSelect.addEventListener('change', actualizarCamposEspecificos);

        // Llamar en carga inicial si hay una tarea en edición
        if (tipoTareaSelect.value) {
            actualizarCamposEspecificos();
        }
    });
    </script>
</body>
</html>