<?php
require_once 'Empleado.php';
require_once 'evaluable.php';
// Esta clase hereda de empleado e implementa evaluable, igual que la de empleado
class Gerente extends Empleado implements evaluable  {
    private $Departamento;
    
    public function __construct($nombre, $ID, $SalarioBase, $Departamento) {
        parent::__construct($nombre, $ID, $SalarioBase);
        $this->Departamento = $Departamento;
    }

    public function getDepartamento() {
        return $this-> Departamento;
    }

    public function setDepartamento($Departamento) {
        $this->Departamento = $Departamento;
    }

    public function AsignarBono($nombre){
        return "Se asigno un bono a: {$nombre}";
    }   
// Aqui esta la implementacion de evaluarDesempenio, especifica para Gerente
    public function evaluarDesempenio() {
        return "El/La Gerente: ".parent::getNombre() . " ha demostrado habilidades destacables gestionando el departamento de: ".$this->Departamento;
    } 
}

        