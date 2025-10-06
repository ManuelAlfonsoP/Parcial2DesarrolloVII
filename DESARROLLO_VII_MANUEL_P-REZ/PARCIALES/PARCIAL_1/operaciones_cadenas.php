<?php
//Recibe una cadena y devuelve un array asociativo con las palabras
function contar_palabras_repetidas($texto){
    $texto = trim($texto);
    $texto = strtolower($texto);
    $texto = explode(" ", $texto);
   foreach ($texto as $palabra){
        $arreglo[$palabra] = 0;
    }
    foreach ($texto as $txt){
        $arreglo[$txt] = $arreglo[$txt] +1;
    }
    
    return $arreglo;
}

//Recibe una cadena de texto y devuelvela con la primera letra de las palabras en mayuscula
function capitalizar_palabra($texto){
    $texto = trim($texto);
    $texto = strtolower($texto);
    $texto = explode(" ", $texto);
    foreach($texto as $txt){
        $prim[] = substr($txt, 0,1);
    }
    foreach($texto as $txt){
        $rest[] = substr($txt,1);
    }
    foreach($prim as $p){
        $may[] = strtoupper($p);
    }
    $a = implode("",$may);
    $a = strlen($a);
    for($i=0; $i<$a;$i++){
        $arrf[$i] = $may[$i].$rest[$i];
    }

    $final = implode(" ", $arrf);
    return $final;
}


$texto = "aaa bbb ccc ddd fff aaa bbb ccc xxx aaa";
$texto = capitalizar_palabra($texto);

//$a = "palabra";
//echo substr($a, 0,1);

?>