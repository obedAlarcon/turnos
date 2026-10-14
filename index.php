
<?php

require_once "configuracion/conexion.php";

try {
    $conexion = Conexion::conectar();

    echo "Conexión a la base de datos exitosa.";

} catch (Exception $e) {
    echo $e->getMessage();
}

