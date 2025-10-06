<?php
//calcula las cuotas mensuales
function calcular_promocion($antiguedad_meses){
    if($antiguedad_meses>=3&&$antiguedad_meses<=12){
        $descuento = .08;
    }elseif($antiguedad_meses>=12&&$antiguedad_meses<=24){
        $descuento = .12;
    }elseif($antiguedad_meses>24){
        $descuento = .20;
    }
}

$prueba = 10;
echo calcular_promocion($prueba);
?>