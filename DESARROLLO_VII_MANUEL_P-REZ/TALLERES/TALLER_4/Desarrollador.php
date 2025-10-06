<?php
require_once 'Empleado.php';
require_once 'evaluable.php';
// Esta clase hereda de empleado e implementa evluable
class Desarrollador extends Empleado implements evaluable{
    private $LenguajeP, $Experiencia;
    
    public function __construct($nombre, $ID, $SalarioBase, $LenguajeP, $Experiencia) {
        parent::__construct($nombre, $ID, $SalarioBase);
        $this->LenguajeP = $LenguajeP;
        $this->Experiencia = $Experiencia;
    }

    public function getLenguaje() {
        return $this->LenguajeP;
    }

    public function setLenguaje($LenguajeP) {
        $this->LenguajeP = $LenguajeP;
    }

    public function getExperiencia() {
        return $this -> Experiencia;
    }

    public function setExperiencia($Experiencia) {
        $this->Experiencia = $Experiencia;
    }
// Aqui esta la implementacion de evaluardesempenio para la clase de desarrollador, esta es distinta que la de gerente
      public function evaluarDesempenio() {
        return "El/La Desarrollador/a: ".parent::getNombre() . " ha demostrado un desempeño destacable trabajando el lenguaje: ".$this->LenguajeP;
    } 
}