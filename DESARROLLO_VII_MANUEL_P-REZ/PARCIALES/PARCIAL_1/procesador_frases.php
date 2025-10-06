<?php
include 'operaciones_cadenas.php';

$frases = ["las manzanas me gustan mucho","aaa bbb ccc ddd fff aaa bbb",
 "no se me ocurre que poner en este arreglo", "me costo algo la parte 1"];

foreach($frases as $f){
    echo "Cantidad de palabras: ";
    print_r(contar_palabras_repetidas($f));
    echo "Frase con inicio en mayuscula: ";
    print_r(capitalizar_palabra($f));
    echo "</br>";
}
?>