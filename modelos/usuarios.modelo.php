
<?php

require_once __DIR__ . "/../configuracion/conexion.php";

class ModeloUsuarios
{
    static public function mdlCrearUsuario($tabla, $datos)
    {
        // Solo se permite utilizar la tabla users.
        if ($tabla !== "users") {
            return "error";
        }

        $conexion = Conexion::conectar();

        $stmt = $conexion->prepare(
            "INSERT INTO users
                (nombre, telefono, password, rol, sede_id, activo)
             VALUES
                (:nombre, :telefono, :password, :rol, :sede_id, 1)"
        );

        $stmt->bindValue(":nombre", $datos["nombre"], PDO::PARAM_STR);
        $stmt->bindValue(":telefono", $datos["telefono"], PDO::PARAM_STR);
        $stmt->bindValue(":password", $datos["password"], PDO::PARAM_STR);
        $stmt->bindValue(":rol", $datos["rol"], PDO::PARAM_STR);

        if (
            !isset($datos["sede_id"]) ||
            $datos["sede_id"] === "" ||
            $datos["sede_id"] === null
        ) {
            $stmt->bindValue(":sede_id", null, PDO::PARAM_NULL);
        } else {
            $stmt->bindValue(
                ":sede_id",
                (int) $datos["sede_id"],
                PDO::PARAM_INT
            );
        }

        return $stmt->execute() ? "ok" : "error";
    }

    static public function mdlMostrarUsuarios($tabla, $item, $valor)
    {
        if ($tabla !== "users") {
            return false;
        }

        $conexion = Conexion::conectar();

        $columnasPermitidas = [
            "id",
            "nombre",
            "telefono",
            "rol",
            "activo",
            "sede_id"
        ];

        if ($item !== null) {
            if (!in_array($item, $columnasPermitidas, true)) {
                return false;
            }

            $stmt = $conexion->prepare(
                "SELECT
                    u.id,
                    u.nombre,
                    u.telefono,
                    u.rol,
                    u.sede_id,
                    u.activo,
                    u.creado_en,
                    s.nombre AS nombre_sede
                 FROM users u
                 LEFT JOIN sedes s ON s.id = u.sede_id
                 WHERE u.`$item` = :valor
                 LIMIT 1"
            );

            $stmt->bindValue(":valor", $valor, PDO::PARAM_STR);
            $stmt->execute();

            return $stmt->fetch(PDO::FETCH_ASSOC);
        }

        $stmt = $conexion->prepare(
            "SELECT
                u.id,
                u.nombre,
                u.telefono,
                u.rol,
                u.sede_id,
                u.activo,
                u.creado_en,
                s.nombre AS nombre_sede
             FROM users u
             LEFT JOIN sedes s ON s.id = u.sede_id
             ORDER BY u.id DESC"
        );

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    static public function mdlListarAdministradores()
    {
        $conexion = Conexion::conectar();

        $stmt = $conexion->prepare(
            "SELECT
                u.id,
                u.nombre,
                u.telefono,
                u.sede_id,
                u.activo,
                u.creado_en,
                s.nombre AS nombre_sede
             FROM users u
             LEFT JOIN sedes s ON s.id = u.sede_id
             WHERE u.rol = 'admin'
             ORDER BY u.id DESC"
        );

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    static public function mdlBuscarPorTelefono($telefono)
    {
        $conexion = Conexion::conectar();

        $stmt = $conexion->prepare(
            "SELECT
                id,
                nombre,
                telefono,
                password,
                rol,
                sede_id,
                activo
             FROM users
             WHERE telefono = :telefono
             LIMIT 1"
        );

        $stmt->bindValue(":telefono", $telefono, PDO::PARAM_STR);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    static public function mdlActualizarEstado($id, $activo)
    {
        $conexion = Conexion::conectar();

        $stmt = $conexion->prepare(
            "UPDATE users
             SET activo = :activo
             WHERE id = :id
               AND rol = 'admin'"
        );

        $stmt->bindValue(":activo", (int) $activo, PDO::PARAM_INT);
        $stmt->bindValue(":id", (int) $id, PDO::PARAM_INT);

        $stmt->execute();

        return $stmt->rowCount() >= 0 ? "ok" : "error";
    }

    static public function mdlActualizarSedeAdministrador($id, $sedeId)
    {
        $conexion = Conexion::conectar();

        $stmt = $conexion->prepare(
            "UPDATE users
             SET sede_id = :sede_id
             WHERE id = :id
               AND rol = 'admin'"
        );

        $stmt->bindValue(":sede_id", (int) $sedeId, PDO::PARAM_INT);
        $stmt->bindValue(":id", (int) $id, PDO::PARAM_INT);

        $stmt->execute();

        return $stmt->rowCount() >= 0 ? "ok" : "error";
    }
}