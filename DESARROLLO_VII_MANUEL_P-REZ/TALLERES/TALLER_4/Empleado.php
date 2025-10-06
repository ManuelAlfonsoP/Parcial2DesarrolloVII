<?php
class Empleado {
    // Aqui se colocan las propiedades del empleado y las funciones para la construccion, getters y setters de cada propiedad.
    private $nombre;
    private $ID;
    private $SalarioBase;

    public function __construct($nombre, $ID, $SalarioBase) {
        $this->setNombre($nombre);
        $this->setID($ID);
        $this->setSalarioBase($SalarioBase);
    }

    public function getNombre() {
        return $this->nombre;
    }

    public function setNombre($nombre) {
        $this->nombre = trim($nombre);
    }

    public function getID() {
        return $this->ID;
    }

    public function setID($ID) {
        $this->ID = trim($ID);
    }

    public function getSalarioBase() {
        return $this->SalarioBase;
    }

    public function setSalarioBase($SalarioBase) {
        $this->SalarioBase = trim($SalarioBase);
    }
}