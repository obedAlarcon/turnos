
<?php

class Conexion
{
    static public function conectar()
    {
        try {
            $host = getenv('DB_HOST') ?: 'localhost';
            $dbname = getenv('DB_NAME') ?: 'turnos';
            $username = getenv('DB_USER') ?: 'root';
            $password = getenv('DB_PASSWORD') ?: '';
            $port = getenv('DB_PORT') ?: '3306';

            $link = new PDO(
                "mysql:host={$host};port={$port};dbname={$dbname};charset=utf8mb4",
                $username,
                $password
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
            error_log(
                "Error de conexión a la base de datos: " . $e->getMessage()
            );

            throw new Exception(
                "No fue posible conectar con la base de datos."
            );
        }
    }
}
