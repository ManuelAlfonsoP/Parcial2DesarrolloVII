<?php
// Leer Inventario desde el archivo JSON y convertirlo a un array de productos
function leerjson(){
    $contenidoInventario = file_get_contents("inventario.json");
    return json_decode($contenidoInventario, true);
}

// Ordenar el inventario alfabeticamente por nombre del producto
function ordjson($ca){
    usort($ca, function($a,$b){
        return strcmp($a['nombre'], $b['nombre']);
    });
    return $ca;
}

// Mostrar un resumen del inventario ordenado (Nombre, precio, cantidad)
function resinv($ca){
    foreach ($ca as $c){
        echo "- " . $c['nombre'] . "; Precio:" . $c['precio']. "; Cantidad:". $c['cantidad']." </br>";
    }
}

// Calcular el valor total del inventario
function totalinv($ca){
    return array_sum(array_map(function ($ca){
         return $ca['cantidad'] * $ca['precio']; 
        }, $ca));
}

// Informe de productos con stock bajo (menos de 5 unidades en este caso)
function bajos($ca){
    $bajo = array_filter($ca, function($ca){
        return $ca['cantidad'] < 5;
    });

    echo "</br></br> Productos con stock bajo:</br>";
    if (count($bajo) > 0){
        foreach($bajo as $b){
            echo "</br>- " . $b['nombre'] ."; Cantidad:". $b['cantidad'];
        }
    }else{
        echo "</br>- No hay productos con stock bajo.";
    }

}

//Script Principal
$ca = leerjson();
$ca = ordjson($ca);
echo "Inventario:</br></br>";
resinv($ca);
echo "</br>Total del inventario: ".totalinv($ca);
bajos($ca);
?>
          

