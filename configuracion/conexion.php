
<?php

class Conexion
{
    static public function conectar()
    {
        try {
            $link = new PDO(
                "mysql:host=localhost;dbname=turnos;charset=utf8mb4",
                "root",
                ""
            );

            $link->setAttribute(
                PDO::ATTR_ERRMODE,
                PDO::ERRMODE_EXCEPTION
            );

            $link->setAttribute(
                PDO::ATTR_DEFAULT_FETCH_MODE,
                PDO::FETCH_ASSOC
            );

            $link->setAttribute(
                PDO::ATTR_EMULATE_PREPARES,
                false
            );

            return $link;

        } catch (PDOException $e) {
            error_log("Error de conexión a la base de datos: " . $e->getMessage());

            throw new Exception(
                "No fue posible conectar con la base de datos."
            );
        }
    }
}
