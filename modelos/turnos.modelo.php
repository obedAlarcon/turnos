
<?php

require_once __DIR__ . "/../configuracion/conexion.php";

class ModeloTurnos
{
    static public function mdlRegistrarTurno($datos)
    {
        $conexion = Conexion::conectar();

        $stmt = $conexion->prepare(
            "SELECT id
             FROM sedes
             WHERE id = :sede_id AND activa = 1
             LIMIT 1"
        );

        $stmt->bindValue(":sede_id", $datos["sede_id"], PDO::PARAM_INT);
        $stmt->execute();

        if (!$stmt->fetch()) {
            throw new Exception("La sede seleccionada no está activa.");
        }

        $stmt = $conexion->prepare(
            "INSERT INTO turnos
                (user_id, sede_id, tipo, numero,
                 nombre_cliente, telefono_cliente, fecha_turno)
             VALUES
                (NULL, :sede_id, :tipo, :numero,
                 :nombre, :telefono, CURDATE())"
        );

        $stmt->bindValue(":sede_id", $datos["sede_id"], PDO::PARAM_INT);
        $stmt->bindValue(":tipo", $datos["tipo"], PDO::PARAM_STR);
        $stmt->bindValue(":numero", $datos["numero"], PDO::PARAM_INT);
        $stmt->bindValue(":nombre", $datos["nombre"], PDO::PARAM_STR);
        $stmt->bindValue(":telefono", $datos["telefono"], PDO::PARAM_STR);
        $stmt->execute();

        return $conexion->lastInsertId();
    }

    static public function mdlListarTurnosHoy($sedeId = 1)
    {
        $conexion = Conexion::conectar();

        $stmt = $conexion->prepare(
            "SELECT id, tipo, numero, nombre_cliente,
                    telefono_cliente, fecha_turno, creado_en
             FROM turnos
             WHERE sede_id = :sede_id
               AND fecha_turno = CURDATE()
             ORDER BY FIELD(tipo, 'G', 'R', 'C'), numero ASC"
        );

        $stmt->bindValue(":sede_id", $sedeId, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    static public function mdlConsultarTurnosPorTelefono($telefono, $sedeId = 1)
    {
        $conexion = Conexion::conectar();

        $stmt = $conexion->prepare(
            "SELECT
                t.tipo,
                t.numero,
                t.fecha_turno,
                CASE t.tipo
                    WHEN 'G' THEN s.turno_actual_g
                    WHEN 'R' THEN s.turno_actual_r
                    WHEN 'C' THEN s.turno_actual_c
                END AS turno_actual
             FROM turnos t
             INNER JOIN sedes s ON s.id = t.sede_id
             WHERE t.telefono_cliente = :telefono
               AND t.sede_id = :sede_id
               AND t.fecha_turno = CURDATE()
             ORDER BY FIELD(t.tipo, 'G', 'R', 'C'), t.numero ASC"
        );

        $stmt->bindValue(":telefono", $telefono, PDO::PARAM_STR);
        $stmt->bindValue(":sede_id", $sedeId, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }
}
