<?php
    $nombre = "Manuel Alfonso Perez Arroyo";
    $edad = 27;
    $correo = "ManuelAlfonsoP@hotmail.com";
    $telefono = 66908914;
    define("OCUPACION", "Estudiante");

    $parte1 = "Hola, me llamo " .$nombre. " y tengo " . $edad . " años.";
    echo $parte1 . "<br>";
    print "Mi correo es: $correo<br>";
    printf("Mi telefono es %d<br>", $telefono);
    print "y soy un: ".OCUPACION."<br>";

    var_dump($nombre);
    echo "<br>";
    var_dump($edad);
    echo "<br>";
    var_dump($correo);
    echo "<br>";
    var_dump($telefono);
    echo "<br>";
    var_dump(OCUPACION);
    echo "<br>";
?>
