<?php
require_once "Gerente.php";
require_once "Desarrollador.php";
// La clase empresa utiliza ambas clases dentro de si misma
class Empresa {
    // lo que hacemos es crear un arreglo que almacene los empleados una vez estos son creados
    private $empleados = [];
    // creamos una funcion para añadir empleados al arreglo
    public function agregarEmpleado(Empleado $empleado) {
        $this->empleados[] = $empleado;
    }
    // una funcion para enlistar uno por uno los empleados y datos de ellos
     public function listarEmpleados() {
        foreach ($this->empleados as $empleado) {
            echo "Empleado: " . $empleado->getNombre() . " ID: " . $empleado->getId() . " Salario base: " . $empleado->getSalarioBase() . "<br>";
        }
    }
    // una funcion que suma los salarios de todos los empleados añadidos a la empresa
    public function calcularNomina() {
        $total = 0;
        foreach ($this->empleados as $emp) {
            $total += $emp->getSalarioBase();
        }
        return $total;
    }
    // y una funcion que realiza la evaluacion en cada uno de los empleados
    public function evaluarEmpleados() {
        foreach ($this->empleados as $emp){
            if($emp instanceof evaluable){
                echo $emp->evaluardesempenio() ."<br>";        
            }
            
        }
    }
}