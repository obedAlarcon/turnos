
<?php

require_once __DIR__ . "/../modelos/turnos.modelo.php";

class ControladorTurnos
{
    static public function ctrRegistrarTurno($datos)
    {
        $nombre = trim($datos["nombre"] ?? "");
        $telefono = trim($datos["telefono"] ?? "");
        $numero = filter_var($datos["numero"] ?? null, FILTER_VALIDATE_INT);
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

        $sedeId = 1;

        try {
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
        return ModeloTurnos::mdlListarTurnosHoy(1);
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
            $turnos = ModeloTurnos::mdlConsultarTurnosPorTelefono(
                $telefono,
                1
            );

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
        }
    }
}
