<?php
require_once "Empresa.php";
// Instanciamos la clase empresa
$empresa = new Empresa();
// se crea un gerente y dos desarrolladores
$gerente1 = new Gerente("Laura Martínez", 101, 5000, "Ventas");
$des1 = new Desarrollador("Carlos Gómez", 201, 3000, "PHP", "Senior");
$des2 = new Desarrollador("Ana Torres", 202, 2500, "JavaScript", "Junior");
// y se agregan a la empresa
$empresa->agregarEmpleado($gerente1);
$empresa->agregarEmpleado($des1);
$empresa->agregarEmpleado($des2);
// Una vez añadidos, se listan los empleados, se imprime la nomina y tambien las evaluaciones de desempeño
echo "<h2>Lista de empleados:</h2>";
$empresa->listarEmpleados();
echo "<h2>Nómina total:</h2>";
echo "Total a pagar: $" . $empresa->calcularNomina() . "<br>";
echo "<h2>Evaluaciones de desempeño:</h2>";
$empresa->evaluarEmpleados();