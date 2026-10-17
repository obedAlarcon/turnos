
<?php

require_once __DIR__ . "/../modelos/turnos.modelo.php";
require_once __DIR__ . "/../configuracion/seguridad.php";

class ControladorTurnos
{
    /**
     * Obtiene la sede del usuario autenticado.
     * El superadministrador debe seleccionar una sede explícitamente
     * antes de realizar operaciones que requieran una sede.
     */
    private static function obtenerSedeActual(): int
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $sedeId = $_SESSION["usuario"]["sede_id"] ?? null;

        if ($sedeId === null || (int) $sedeId < 1) {
            throw new Exception(
                "Tu usuario no tiene una sede asignada. Contacta al superadministrador."
            );
        }

        return (int) $sedeId;
    }

    static public function ctrRegistrarTurno($datos)
    {
        $nombre = trim($datos["nombre"] ?? "");
        $telefono = trim($datos["telefono"] ?? "");
        $numero = filter_var(
            $datos["numero"] ?? null,
            FILTER_VALIDATE_INT
        );
        $tipo = $datos["tipo"] ?? "";

        if ($nombre === "" || mb_strlen($nombre) > 120) {
            return [
                "estado" => "error",
                "mensaje" => "Ingresa un nombre válido de máximo 120 caracteres."
            ];
        }

        if (!preg_match('/^[0-9]{7,15}$/', $telefono)) {
            return [
                "estado" => "error",
                "mensaje" => "Ingresa un celular válido, solo con números."
            ];
        }

        if ($numero === false || $numero === null || $numero < 1) {
            return [
                "estado" => "error",
                "mensaje" => "El número de ficha debe ser mayor que cero."
            ];
        }

        if (!in_array($tipo, ["G", "R", "C"], true)) {
            return [
                "estado" => "error",
                "mensaje" => "Selecciona un tipo de turno válido."
            ];
        }

        try {
            $sedeId = self::obtenerSedeActual();

            $id = ModeloTurnos::mdlRegistrarTurno([
                "sede_id" => $sedeId,
                "tipo" => $tipo,
                "numero" => $numero,
                "nombre" => $nombre,
                "telefono" => $telefono
            ]);

            return [
                "estado" => "ok",
                "mensaje" => "Ficha registrada correctamente.",
                "id" => $id
            ];

        } catch (PDOException $e) {
            if ($e->getCode() === "23000") {
                return [
                    "estado" => "error",
                    "mensaje" => "Esa ficha ya está registrada para esta sede, tipo y fecha."
                ];
            }

            error_log("Error al registrar turno: " . $e->getMessage());

            return [
                "estado" => "error",
                "mensaje" => "No fue posible registrar la ficha."
            ];

        } catch (Exception $e) {
            error_log("Error al registrar turno: " . $e->getMessage());

            return [
                "estado" => "error",
                "mensaje" => $e->getMessage()
            ];
        }
    }

    
static public function ctrListarTurnosHoy()
{
    try {
        $sedeId = self::obtenerSedeActual();

        return ModeloTurnos::mdlListarTurnosHoy($sedeId);

    } catch (Exception $e) {
        error_log("Error al listar turnos: " . $e->getMessage());
        return [];
    }
}


static public function ctrConsultarTurnosPorTelefono($telefono)
{
    $telefono = trim($telefono);

    if (!preg_match('/^[0-9]{7,15}$/', $telefono)) {
        return [
            "estado" => "error",
            "mensaje" => "Ingresa un celular válido, solo con números.",
            "turnos" => []
        ];
    }

    try {
        // Los clientes consultan con su teléfono, sin iniciar sesión
        // y sin depender de la sede del administrador.
        $turnos = ModeloTurnos::mdlConsultarTurnosPorTelefono($telefono);

        return [
            "estado" => "ok",
            "mensaje" => count($turnos) > 0
                ? "Encontramos tus fichas de hoy."
                : "No encontramos fichas para ese celular durante la jornada de hoy.",
            "turnos" => $turnos
        ];

    } catch (PDOException $e) {
        error_log("Error al consultar turnos: " . $e->getMessage());

        return [
            "estado" => "error",
            "mensaje" => "No fue posible consultar los turnos en este momento.",
            "turnos" => []
        ];

    } catch (Exception $e) {
        error_log("Error al consultar turnos: " . $e->getMessage());

        return [
            "estado" => "error",
            "mensaje" => "No fue posible consultar los turnos en este momento.",
            "turnos" => []
        ];
    }
}

}
