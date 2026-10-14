
<?php

require_once __DIR__ . "/../configuracion/conexion.php";

class ModeloSedes
{
    static public function mdlMostrarSedes()
    {
        $conexion = Conexion::conectar();

        $stmt = $conexion->prepare(
            "SELECT id, nombre, direccion, activa,
                    turno_actual_g, turno_actual_r, turno_actual_c
             FROM sedes
             ORDER BY id DESC"
        );

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    static public function mdlMostrarSedePorId($id)
    {
        $conexion = Conexion::conectar();

        $stmt = $conexion->prepare(
            "SELECT id, nombre, direccion, activa,
                    turno_actual_g, turno_actual_r, turno_actual_c
             FROM sedes
             WHERE id = :id
             LIMIT 1"
        );

        $stmt->bindValue(":id", (int) $id, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    static public function mdlCrearSede($datos)
    {
        $conexion = Conexion::conectar();

        $stmt = $conexion->prepare(
            "INSERT INTO sedes (nombre, direccion)
             VALUES (:nombre, :direccion)"
        );

        $stmt->bindValue(":nombre", $datos["nombre"], PDO::PARAM_STR);

        if ($datos["direccion"] === "") {
            $stmt->bindValue(":direccion", null, PDO::PARAM_NULL);
        } else {
            $stmt->bindValue(
                ":direccion",
                $datos["direccion"],
                PDO::PARAM_STR
            );
        }

        return $stmt->execute() ? "ok" : "error";
    }

    static public function mdlActualizarSede($datos)
    {
        $conexion = Conexion::conectar();

        $stmt = $conexion->prepare(
            "UPDATE sedes
             SET nombre = :nombre,
                 direccion = :direccion
             WHERE id = :id"
        );

        $stmt->bindValue(":nombre", $datos["nombre"], PDO::PARAM_STR);

        if ($datos["direccion"] === "") {
            $stmt->bindValue(":direccion", null, PDO::PARAM_NULL);
        } else {
            $stmt->bindValue(
                ":direccion",
                $datos["direccion"],
                PDO::PARAM_STR
            );
        }

        $stmt->bindValue(":id", (int) $datos["id"], PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->rowCount() >= 0 ? "ok" : "error";
    }

    static public function mdlCambiarEstadoSede($id, $activa)
    {
        $conexion = Conexion::conectar();

        $stmt = $conexion->prepare(
            "UPDATE sedes
             SET activa = :activa
             WHERE id = :id"
        );

        $stmt->bindValue(":activa", (int) $activa, PDO::PARAM_INT);
        $stmt->bindValue(":id", (int) $id, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->rowCount() >= 0 ? "ok" : "error";
    }

    static public function mdlActualizarTurnos($datos)
    {
        $conexion = Conexion::conectar();

        // Verificar que la sede exista y esté activa.
        $verificar = $conexion->prepare(
            "SELECT id
             FROM sedes
             WHERE id = :id AND activa = 1
             LIMIT 1"
        );

        $verificar->bindValue(
            ":id",
            (int) $datos["id"],
            PDO::PARAM_INT
        );

        $verificar->execute();

        if (!$verificar->fetch(PDO::FETCH_ASSOC)) {
            return "no_disponible";
        }

        $stmt = $conexion->prepare(
            "UPDATE sedes
             SET turno_actual_g = :general,
                 turno_actual_r = :rapido,
                 turno_actual_c = :cita
             WHERE id = :id AND activa = 1"
        );

        $stmt->bindValue(
            ":general",
            (int) $datos["general"],
            PDO::PARAM_INT
        );

        $stmt->bindValue(
            ":rapido",
            (int) $datos["rapido"],
            PDO::PARAM_INT
        );

        $stmt->bindValue(
            ":cita",
            (int) $datos["cita"],
            PDO::PARAM_INT
        );

        $stmt->bindValue(":id", (int) $datos["id"], PDO::PARAM_INT);
        $stmt->execute();

        return "ok";
    }
}
