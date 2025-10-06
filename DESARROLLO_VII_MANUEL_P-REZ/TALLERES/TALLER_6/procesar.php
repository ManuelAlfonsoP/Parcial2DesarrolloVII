<?php
require_once 'validaciones.php';
require_once 'sanitizacion.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $errores = [];
    $datos = [];

    // Procesar y validar cada campo
    $campos = ['nombre','fechanacimiento', 'email','edad', 'genero', 'intereses', 'comentarios', 'sitioWeb'];
    foreach ($campos as $campo) {
        if (isset($_POST[$campo])) {
            if($campo !== 'edad'){
                $valor = $_POST[$campo];
                $valorSanitizado = call_user_func("sanitizar" . ucfirst($campo), $valor);
                $datos[$campo] = $valorSanitizado;
                if($campo === 'fechanacimiento'){
                    $fn = new DateTime($_POST['fechanacimiento']);
                    $hoy = new DateTime();
                    $edad = $hoy->diff($fn)->y;
                    $datos['edad'] = $edad;
                    if (!call_user_func("validarEdad", $edad)) {
                        $errores[] = "El campo Edad no es válido.";
                    }
                    
                }
            
            if (!call_user_func("validar" . ucfirst($campo), $valorSanitizado)) {
                $errores[] = "El campo $campo no es válido.";
            }
            }
        }
    }

    // Procesar la foto de perfil
    if (isset($_FILES['foto_perfil']) && $_FILES['foto_perfil']['error'] !== UPLOAD_ERR_NO_FILE) {
        if (!validarFotoPerfil($_FILES['foto_perfil'])) {
            $errores[] = "La foto de perfil no es válida.";
        } else {
            $nombreOriginal = pathinfo($_FILES['foto_perfil']['name'], PATHINFO_FILENAME);
            $extension = pathinfo($_FILES['foto_perfil']['name'], PATHINFO_EXTENSION);
            $nombreUnico = $nombreOriginal . '_' . uniqid() . '.' . $extension;
            $rutaDestino = 'uploads/' . $nombreUnico;
            if (move_uploaded_file($_FILES['foto_perfil']['tmp_name'], $rutaDestino)) {
                $datos['foto_perfil'] = $rutaDestino;
            } else {
                $errores[] = "Hubo un error al subir la foto de perfil.";
            }
        }
    }

    // Mostrar resultados o errores
    if (empty($errores)) {
        var_dump($datos);
    echo "<h2>Datos Recibidos:</h2>";
    echo "<table border='1'>";
    foreach ($datos as $campo => $valor) {
        echo "<tr>";
        if ($campo === 'fechanacimiento'){
            echo "<th>" . 'Fecha de Nacimiento' . "</th>";
        }else{
            echo "<th>" . ucfirst($campo) . "</th>";
        }

        if ($campo === 'intereses') {
            echo "<td>" . implode(", ", $valor) . "</td>";
        } elseif ($campo === 'foto_perfil') {
            echo "<td><img src='$valor' width='100'></td>";
        } else {
            echo "<td>$valor</td>";
        }
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<h2>Errores:</h2>";
    echo "<ul>";
    foreach ($errores as $error) {
        echo "<li>$error</li>";
    }
    echo "</ul>";
}

// Archivo donde se guardarán todos los registros
$archivoRegistros = 'registros.json';

// Leer registros existentes
$registros = [];
if (file_exists($archivoRegistros)) {
    $contenido = file_get_contents($archivoRegistros);
    $registros = json_decode($contenido, true) ?? [];
}

// Agregar el nuevo registro
$registros[] = $datos;

// Guardar todo de nuevo en el archivo
file_put_contents($archivoRegistros, json_encode($registros, JSON_PRETTY_PRINT));

echo "<br><a href='formulario.html'>Volver al formulario</a>";
} else {
    echo "Acceso no permitido.";
}
?>
    