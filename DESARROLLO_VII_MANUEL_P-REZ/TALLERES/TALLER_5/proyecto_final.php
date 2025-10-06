<?php
class Estudiante {
    public int $id;
    public string $nombre;
    public int $edad;
    public string $carrera;
    public array $materias;
    public array $flags;
    
    public function __construct(int $id, string $nombre, int $edad, string $carrera) {
        $this->id = $id;
        $this->nombre = $nombre;
        $this->edad = $edad;
        $this->carrera = $carrera;
        $this->materias = [];
        $this->flags = [];
    }

    public function agregarMateria(string $materia, float $calificacion): void {
        $this->materias[$materia] = $calificacion;
        $this->actualizarFlags();
    }

    public function obtenerPromedio(): float {
        if (empty($this->materias)) return 0;
        return array_sum($this->materias) / count($this->materias);
    }

    public function obtenerDetalles(): array {
        return [
            'id' => $this->id,
            'nombre' => $this->nombre,
            'edad' => $this->edad,
            'carrera' => $this->carrera,
            'materias' => $this->materias,
            'promedio' => $this->obtenerPromedio(),
            'flags' => $this->flags
        ];
    }

    public function __toString(): string {
        $materiasStr = "";
        foreach ($this->materias as $materia => $nota) {
            $materiasStr .= "$materia: $nota, ";
        }
        $materiasStr = rtrim($materiasStr, ", ");
        return "ID: $this->id | Nombre: $this->nombre | Edad: $this->edad | Carrera: $this->carrera | Promedio: " 
               . number_format($this->obtenerPromedio(), 2) . " | Materias: [$materiasStr] | Flags: [" . implode(", ", $this->flags) . "]";
    }

    // Actualiza flags según condiciones
    private function actualizarFlags(): void {
        $promedio = $this->obtenerPromedio();
        $this->flags = []; // Resetear flags
        if ($promedio >= 90) $this->flags[] = 'Honor Roll';
        if ($promedio < 75) $this->flags[] = 'En riesgo académico';
        foreach ($this->materias as $nota) {
            if ($nota < 60 && !in_array('Reprobado', $this->flags)) {
                $this->flags[] = 'Reprobado';
            }
        }
    }
}

class SistemaGestionEstudiantes {
    private array $estudiantes;
    private array $graduados;

    public function __construct() {
        $this->estudiantes = [];
        $this->graduados = [];
    }

    public function agregarEstudiante(Estudiante $estudiante): void {
        $this->estudiantes[$estudiante->id] = $estudiante;
    }

    public function obtenerEstudiante(int $id): ?Estudiante {
        return $this->estudiantes[$id] ?? null;
    }

    public function listarEstudiantes(): array {
        return array_values($this->estudiantes);
    }

    public function calcularPromedioGeneral(): float {
        if (empty($this->estudiantes)) return 0;
        return array_sum(array_map(fn($e) => $e->obtenerPromedio(), $this->estudiantes)) / count($this->estudiantes);
    }

    public function obtenerEstudiantesPorCarrera(string $carrera): array {
        return array_filter($this->estudiantes, fn($e) => strcasecmp($e->carrera, $carrera) === 0);
    }

    public function obtenerMejorEstudiante(): ?Estudiante {
        if (empty($this->estudiantes)) return null;
        return array_reduce($this->estudiantes, fn($mejor, $actual) => 
            !$mejor || $actual->obtenerPromedio() > $mejor->obtenerPromedio() ? $actual : $mejor
        );
    }

    public function generarReporteRendimiento(): array {
        $reporte = [];
        foreach ($this->estudiantes as $e) {
            foreach ($e->materias as $materia => $nota) {
                if (!isset($reporte[$materia])) {
                    $reporte[$materia] = ['promedio' => 0, 'max' => $nota, 'min' => $nota, 'count' => 0];
                }
                $reporte[$materia]['promedio'] += $nota;
                $reporte[$materia]['max'] = max($reporte[$materia]['max'], $nota);
                $reporte[$materia]['min'] = min($reporte[$materia]['min'], $nota);
                $reporte[$materia]['count']++;
            }
        }
        foreach ($reporte as $materia => &$datos) {
            $datos['promedio'] = $datos['count'] > 0 ? $datos['promedio'] / $datos['count'] : 0;
            unset($datos['count']);
        }
        return $reporte;
    }

    public function graduarEstudiante(int $id): void {
        if (!isset($this->estudiantes[$id])) return;
        $this->graduados[$id] = $this->estudiantes[$id];
        unset($this->estudiantes[$id]);
    }

    public function generarRanking(): array {
        $ranking = $this->estudiantes;
        usort($ranking, fn($a, $b) => $b->obtenerPromedio() <=> $a->obtenerPromedio());
        return $ranking;
    }

    public function buscarEstudiantes(string $termino): array {
        $termino = strtolower($termino);
        return array_filter($this->estudiantes, fn($e) => 
            strpos(strtolower($e->nombre), $termino) !== false || 
            strpos(strtolower($e->carrera), $termino) !== false
        );
    }

    public function estadisticasPorCarrera(): array {
        $carreras = [];
        foreach ($this->estudiantes as $e) {
            $carrera = $e->carrera;
            if (!isset($carreras[$carrera])) {
                $carreras[$carrera] = ['num_estudiantes' => 0, 'promedio_general' => 0, 'mejor_estudiante' => null];
            }
            $carreras[$carrera]['num_estudiantes']++;
            $carreras[$carrera]['promedio_general'] += $e->obtenerPromedio();
            if (!$carreras[$carrera]['mejor_estudiante'] || $e->obtenerPromedio() > $carreras[$carrera]['mejor_estudiante']->obtenerPromedio()) {
                $carreras[$carrera]['mejor_estudiante'] = $e;
            }
        }
        foreach ($carreras as $carrera => &$datos) {
            $datos['promedio_general'] = $datos['num_estudiantes'] > 0 ? $datos['promedio_general'] / $datos['num_estudiantes'] : 0;
        }
        return $carreras;
    }
}

// Sección de prueba
$sistema = new SistemaGestionEstudiantes();

// Crear 10 estudiantes con distintas carreras y calificaciones
$estudiantes = [
    new Estudiante(1, "Ana López", 20, "Ingeniería"),
    new Estudiante(2, "Carlos Gómez", 22, "Medicina"),
    new Estudiante(3, "María Rodríguez", 21, "Derecho"),
    new Estudiante(4, "Pedro Pérez", 23, "Ingeniería"),
    new Estudiante(5, "Laura Díaz", 20, "Medicina"),
    new Estudiante(6, "Luis Fernández", 24, "Derecho"),
    new Estudiante(7, "Marta Sánchez", 22, "Ingeniería"),
    new Estudiante(8, "Jorge Ramírez", 21, "Medicina"),
    new Estudiante(9, "Lucía Torres", 20, "Derecho"),
    new Estudiante(10, "Diego Morales", 23, "Ingeniería")
];

// Agregar materias y calificaciones
foreach ($estudiantes as $e) {
    $e->agregarMateria("Matemáticas", rand(55, 100));
    $e->agregarMateria("Física", rand(55, 100));
    $e->agregarMateria("Química", rand(55, 100));
    $sistema->agregarEstudiante($e);
}

// Mostrar estudiantes
echo "Listado de estudiantes:<br>";
foreach ($sistema->listarEstudiantes() as $e) {
    echo $e . "<br>";
}

// Promedio general
echo "<br><br>Promedio general de la clase: " . number_format($sistema->calcularPromedioGeneral(), 2) . "\n";

// Mejor estudiante
$mejor = $sistema->obtenerMejorEstudiante();
echo "<br><br>Mejor estudiante: " . $mejor->nombre . " con promedio " . number_format($mejor->obtenerPromedio(), 2) . "\n";

// Generar reporte de rendimiento
$reporte = $sistema->generarReporteRendimiento();
echo "<br><br>Reporte de rendimiento por materia:<br>";
foreach ($reporte as $materia => $datos) {
    echo "$materia -> Promedio: " . number_format($datos['promedio'],2) . ", Max: {$datos['max']}, Min: {$datos['min']}<br>";
}

// Ranking
echo "<br>Ranking de estudiantes:<br>";
foreach ($sistema->generarRanking() as $idx => $e) {
    echo ($idx+1) . ". " . $e->nombre . " - Promedio: " . number_format($e->obtenerPromedio(),2) . "<br>";
}

// Estadísticas por carrera
$stats = $sistema->estadisticasPorCarrera();
echo "<br>Estadísticas por carrera:<br>";
foreach ($stats as $carrera => $datos) {
    echo "$carrera -> Número de estudiantes: {$datos['num_estudiantes']}, Promedio: " . number_format($datos['promedio_general'],2) . ", Mejor estudiante: {$datos['mejor_estudiante']->nombre}<br>";
}

// Ejemplo de búsqueda
$resultadosBusqueda = $sistema->buscarEstudiantes("ingeniería");
echo "<br>Resultados de búsqueda para 'Ingeniería':<br>";
foreach ($resultadosBusqueda as $e) {
    echo $e . "<br>";
}

// Graduar estudiante
$sistema->graduarEstudiante(1);
echo "<br>Después de graduar al estudiante con ID 1:<br>";
foreach ($sistema->listarEstudiantes() as $e) {
    echo $e . "<br>";
}

?>
