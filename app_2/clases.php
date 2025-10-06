<?php
// (1) Crear la interfaz Prestable
interface Prestable {
    public function obtenerDetallesPrestamo(): string;
}

// (2) Modificar RecursoBiblioteca a abstract e implementar Prestable
abstract class RecursoBiblioteca implements Prestable {
    public $id;
    public $titulo;
    public $autor;
    public $anioPublicacion;
    public $estado;
    public $fechaAdquisicion;
    public $tipo;

    public function __construct($datos) {
        foreach ($datos as $key => $value) {
            if (property_exists($this, $key)) {
                $this->$key = $value;
            }
        }
    }

    // El método abstracto se implementará en las clases hijas
    abstract public function obtenerDetallesPrestamo(): string;
}

// (3) Crear la clase Libro que hereda de RecursoBiblioteca y agrega isbn
class Libro extends RecursoBiblioteca {
    public $isbn;

    public function __construct($datos) {
        parent::__construct($datos);
        $this->isbn = $datos['isbn'] ?? null;
    }

    // (4) Implementar obtenerDetallesPrestamo() para Libro
    public function obtenerDetallesPrestamo(): string {
        return "ISBN: " . ($this->isbn ?? 'N/A');
    }
}

// (3) Crear la clase Revista que hereda de RecursoBiblioteca y agrega numeroEdicion
class Revista extends RecursoBiblioteca {
    public $numeroEdicion;

    public function __construct($datos) {
        parent::__construct($datos);
        // Asegurar que numeroEdicion sea un entero
        $this->numeroEdicion = isset($datos['numeroEdicion']) ? intval($datos['numeroEdicion']) : null;
    }

    // (4) Implementar obtenerDetallesPrestamo() para Revista
    public function obtenerDetallesPrestamo(): string {
        return "Edición: " . ($this->numeroEdicion ?? 'N/A');
    }
}

// (3) Crear la clase DVD que hereda de RecursoBiblioteca y agrega duracion
class DVD extends RecursoBiblioteca {
    public $duracion;

    public function __construct($datos) {
        parent::__construct($datos);
        // Asegurar que duracion sea un entero
        $this->duracion = isset($datos['duracion']) ? intval($datos['duracion']) : null;
    }

    // (4) Implementar obtenerDetallesPrestamo() para DVD
    public function obtenerDetallesPrestamo(): string {
        return "Duración: " . ($this->duracion ? $this->duracion . " min" : 'N/A');
    }
}

class GestorBiblioteca {
    private $recursos = [];
    private $archivoJson = 'biblioteca.json'; // Nombre del archivo JSON

    // (6) Modificar cargarRecursos() para usar las nuevas clases hijas
    public function cargarRecursos(): array {
        if (!file_exists($this->archivoJson)) {
            $this->recursos = [];
            return $this->recursos;
        }

        $json = file_get_contents($this->archivoJson);
        $data = json_decode($json, true) ?? [];
        $this->recursos = [];
        
        foreach ($data as $recursoData) {
            $tipo = $recursoData['tipo'] ?? null;
            $recurso = null;

            switch ($tipo) {
                case 'Libro':
                    $recurso = new Libro($recursoData);
                    break;
                case 'Revista':
                    $recurso = new Revista($recursoData);
                    break;
                case 'DVD':
                    $recurso = new DVD($recursoData);
                    break;
                default:
                    // Manejo básico para recursos desconocidos
                    $recurso = new class($recursoData) extends RecursoBiblioteca {
                        public function obtenerDetallesPrestamo(): string { return "Tipo de recurso desconocido"; }
                    };
                    break;
            }
            $this->recursos[] = $recurso;
        }
        
        return $this->recursos;
    }

    // (12) Método para guardar los recursos en el JSON
    private function guardarRecursos() {
        $data = [];
        foreach ($this->recursos as $recurso) {
            $data[] = (array) $recurso; // Convertir objeto a array para guardar
        }
        file_put_contents($this->archivoJson, json_encode($data, JSON_PRETTY_PRINT));
    }

    // (7) Implementar agregarRecurso
    public function agregarRecurso(RecursoBiblioteca $recurso) {
        // Asignar un nuevo ID. Buscar el ID más grande y sumar 1
        $maxId = 0;
        foreach ($this->recursos as $r) {
            if ($r->id > $maxId) {
                $maxId = $r->id;
            }
        }
        $recurso->id = $maxId + 1;

        $this->recursos[] = $recurso;
        $this->guardarRecursos(); // (12) Actualizar JSON
    }

    // (7) Implementar eliminarRecurso
    public function eliminarRecurso($id) {
        $id = intval($id);
        $this->recursos = array_filter($this->recursos, function($recurso) use ($id) {
            return $recurso->id !== $id;
        });
        // Reindexar el array después de filtrar
        $this->recursos = array_values($this->recursos); 
        $this->guardarRecursos(); // (12) Actualizar JSON
        return true;
    }

    // (7) Implementar actualizarRecurso
    public function actualizarRecurso(RecursoBiblioteca $recursoActualizado) {
        foreach ($this->recursos as $key => $recursoExistente) {
            if ($recursoExistente->id === $recursoActualizado->id) {
                $this->recursos[$key] = $recursoActualizado;
                $this->guardarRecursos(); // (12) Actualizar JSON
                return true;
            }
        }
        return false;
    }

    // (7) Implementar actualizarEstadoRecurso
    public function actualizarEstadoRecurso($id, $nuevoEstado) {
        $id = intval($id);
        foreach ($this->recursos as $recurso) {
            if ($recurso->id === $id) {
                $recurso->estado = $nuevoEstado;
                $this->guardarRecursos(); // (12) Actualizar JSON
                return true;
            }
        }
        return false;
    }

    // (7) Implementar buscarRecursosPorEstado
    public function buscarRecursosPorEstado($estado) {
        return array_filter($this->recursos, function($recurso) use ($estado) {
            return $recurso->estado === $estado;
        });
    }

    // (7) Implementar listarRecursos con lógica de filtrado y ordenamiento
    public function listarRecursos($filtroEstado = '', $campoOrden = 'id', $direccionOrden = 'ASC'): array {
        $lista = $this->recursos;

        // Filtrado por estado
        if (!empty($filtroEstado)) {
            $lista = $this->buscarRecursosPorEstado($filtroEstado);
            $lista = array_values($lista); // Reindexar
        }

        // Ordenamiento
        usort($lista, function($a, $b) use ($campoOrden, $direccionOrden) {
            $valorA = $a->$campoOrden ?? '';
            $valorB = $b->$campoOrden ?? '';

            if (is_numeric($valorA) && is_numeric($valorB)) {
                $comparacion = $valorA <=> $valorB;
            } else {
                $comparacion = strcasecmp($valorA, $valorB);
            }

            return $direccionOrden === 'ASC' ? $comparacion : -$comparacion;
        });

        return $lista;
    }
    
    public function obtenerRecursoPorId($id) {
        $id = intval($id);
        foreach ($this->recursos as $recurso) {
            if ($recurso->id === $id) {
                return $recurso;
            }
        }
        return null;
    }
}