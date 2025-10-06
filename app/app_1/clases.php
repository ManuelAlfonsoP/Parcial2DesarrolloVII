<?php
// Archivo: clases.php

// Interfaz para obtener detalles específicos de una tarea
interface Detalle {
    public function obtenerDetallesEspecificos(): string;
}

class Tarea implements Detalle {
    public $id;
    public $titulo;
    public $descripcion;
    public $estado; // 'pendiente', 'en_progreso', 'completada'
    public $prioridad; // 1 (Alta) a 5 (Baja)
    public $fechaCreacion;
    public $tipo; // 'desarrollo', 'diseno', 'testing'
 
    public function __construct($datos) {
        // Asignar propiedades a partir de un arreglo asociativo
        foreach ($datos as $key => $value) {
            if (property_exists($this, $key)) {
                $this->$key = $value;
            }
        }
        // Asegurar que la fecha de creación exista al crear una nueva tarea
        if (!isset($this->fechaCreacion)) {
            $this->fechaCreacion = date('Y-m-d');
        }
    }

    // Método de la interfaz Detalle (implementación base)
    public function obtenerDetallesEspecificos(): string {
        return "N/A";
    }
}

class TareaDesarrollo extends Tarea {
    public $lenguajeProgramacion;

    public function __construct($datos) {
        parent::__construct($datos);
        $this->tipo = 'desarrollo'; // Asegurar el tipo
    }

    public function obtenerDetallesEspecificos(): string {
        return "Lenguaje: " . (isset($this->lenguajeProgramacion) ? $this->lenguajeProgramacion : 'No especificado');
    }
}

class TareaDiseno extends Tarea {
    public $herramientaDiseno;

    public function __construct($datos) {
        parent::__construct($datos);
        $this->tipo = 'diseno'; // Asegurar el tipo
    }

    public function obtenerDetallesEspecificos(): string {
        return "Herramienta: " . (isset($this->herramientaDiseno) ? $this->herramientaDiseno : 'No especificada');
    }
}

class TareaTesting extends Tarea {
    public $tipoTest; // 'unitario', 'integracion', 'e2e'

    public function __construct($datos) {
        parent::__construct($datos);
        $this->tipo = 'testing'; // Asegurar el tipo
    }

    public function obtenerDetallesEspecificos(): string {
        return "Tipo de Test: " . (isset($this->tipoTest) ? $this->tipoTest : 'No especificado');
    }
}

class GestorTareas {
    private $tareas = [];
    private $archivoJson = 'tareas.json';

    public function __construct() {
        // Inicializar el gestor cargando las tareas
        $this->cargarTareas();
    }

    // Carga las tareas desde el JSON, creando la instancia de la clase hija correcta
    public function cargarTareas() {
        $this->tareas = []; // Resetear tareas
        if (!file_exists($this->archivoJson) || filesize($this->archivoJson) == 0) {
            $data = [];
        } else {
            $json = file_get_contents($this->archivoJson);
            $data = json_decode($json, true);
            if ($data === null) {
                $data = []; // Manejar JSON inválido
            }
        }
        
        foreach ($data as $tareaData) {
            switch ($tareaData['tipo']) {
                case 'desarrollo':
                    $tarea = new TareaDesarrollo($tareaData);
                    break;
                case 'diseno':
                    $tarea = new TareaDiseno($tareaData);
                    break;
                case 'testing':
                    $tarea = new TareaTesting($tareaData);
                    break;
                default:
                    $tarea = new Tarea($tareaData);
                    break;
            }
            $this->tareas[] = $tarea;
        }
        
        return $this->tareas;
    }

    // Guarda el arreglo de tareas en el archivo JSON
    private function guardarTareas() {
        // Convertir el arreglo de objetos Tarea a un arreglo asociativo para JSON
        $dataToSave = array_map(function($tarea) {
            return (array) $tarea; // Convertir objeto a arreglo
        }, $this->tareas);
        file_put_contents($this->archivoJson, json_encode($dataToSave, JSON_PRETTY_PRINT));
    }

    // Genera el próximo ID disponible
    private function generarNuevoId() {
        $maxId = 0;
        foreach ($this->tareas as $tarea) {
            if ($tarea->id > $maxId) {
                $maxId = $tarea->id;
            }
        }
        return $maxId + 1;
    }

    // Agrega una nueva tarea
    public function agregarTarea($datos) {
        $datos['id'] = $this->generarNuevoId();
        $datos['estado'] = 'pendiente'; // Estado inicial
        
        switch ($datos['tipo']) {
            case 'desarrollo':
                $nuevaTarea = new TareaDesarrollo($datos);
                break;
            case 'diseno':
                $nuevaTarea = new TareaDiseno($datos);
                break;
            case 'testing':
                $nuevaTarea = new TareaTesting($datos);
                break;
            default:
                $nuevaTarea = new Tarea($datos);
                break;
        }
        
        $this->tareas[] = $nuevaTarea;
        $this->guardarTareas();
    }

    // Elimina una tarea por su ID
    public function eliminarTarea($id) {
        $id = (int) $id;
        $this->tareas = array_filter($this->tareas, function($tarea) use ($id) {
            return $tarea->id !== $id;
        });
        // Re-indexar el arreglo después de filtrar
        $this->tareas = array_values($this->tareas);
        $this->guardarTareas();
    }

    // Busca una tarea por ID
    private function buscarTareaPorId($id) {
        $id = (int) $id;
        foreach ($this->tareas as $tarea) {
            if ($tarea->id === $id) {
                return $tarea;
            }
        }
        return null;
    }

    // Actualiza los datos de una tarea
    public function actualizarTarea($datos) {
        $id = (int) $datos['id'];
        foreach ($this->tareas as &$tarea) {
            if ($tarea->id === $id) {
                // Actualizar solo las propiedades que se pasaron
                foreach ($datos as $key => $value) {
                    if (property_exists($tarea, $key) && $key !== 'id') {
                        $tarea->$key = $value;
                    }
                }
                // Manejar los campos específicos de cada tipo
                if ($tarea instanceof TareaDesarrollo) {
                    $tarea->lenguajeProgramacion = $datos['lenguajeProgramacion'] ?? '';
                } elseif ($tarea instanceof TareaDiseno) {
                    $tarea->herramientaDiseno = $datos['herramientaDiseno'] ?? '';
                } elseif ($tarea instanceof TareaTesting) {
                    $tarea->tipoTest = $datos['tipoTest'] ?? '';
                }
                
                $this->guardarTareas();
                return true;
            }
        }
        return false;
    }

    // Actualiza el estado de una tarea
    public function actualizarEstadoTarea($id, $nuevoEstado) {
        $tarea = $this->buscarTareaPorId($id);
        if ($tarea) {
            $estadosValidos = ['pendiente', 'en_progreso', 'completada'];
            if (in_array($nuevoEstado, $estadosValidos)) {
                $tarea->estado = $nuevoEstado;
                $this->guardarTareas();
                return true;
            }
        }
        return false;
    }

    // Lista todas las tareas, aplicando opcionalmente filtro, ordenamiento, o ambos
    public function listarTareas($filterEstado = '', $sortField = 'id', $sortDirection = 'ASC') {
        $tareasFiltradas = $this->tareas;
        
        // 1. Filtrado
        if (!empty($filterEstado)) {
            $tareasFiltradas = array_filter($tareasFiltradas, function($tarea) use ($filterEstado) {
                return $tarea->estado === $filterEstado;
            });
        }
        
        // 2. Ordenamiento
        usort($tareasFiltradas, function($a, $b) use ($sortField, $sortDirection) {
            $valA = $a->$sortField ?? null;
            $valB = $b->$sortField ?? null;

            if ($valA === $valB) return 0;
            
            $result = ($valA < $valB) ? -1 : 1;
            
            return ($sortDirection === 'DESC') ? -$result : $result;
        });

        return $tareasFiltradas;
    }

    // Necesario para la edición: obtener la tarea por ID
    public function obtenerTareaPorId($id) {
        return $this->buscarTareaPorId($id);
    }
}