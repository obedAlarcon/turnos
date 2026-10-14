
<?php

require_once __DIR__ . "/../modelos/sede.modelo.php";

class ControladorSedes
{
    static public function ctrMostrarSedes()
    {
        return ModeloSedes::mdlMostrarSedes();
    }

    static public function ctrMostrarSedePorId($id)
    {
        $id = filter_var($id, FILTER_VALIDATE_INT);

        if ($id === false || $id === null || $id < 1) {
            return false;
        }

        return ModeloSedes::mdlMostrarSedePorId($id);
    }

    static public function ctrCrearSede($datos)
    {
        $nombre = trim($datos["nombre"] ?? "");
        $direccion = trim($datos["direccion"] ?? "");

        if ($nombre === "" || mb_strlen($nombre, "UTF-8") > 120) {
            return [
                "estado" => "error",
                "mensaje" => "El nombre es obligatorio y no puede superar 120 caracteres."
            ];
        }

        if (mb_strlen($direccion, "UTF-8") > 255) {
            return [
                "estado" => "error",
                "mensaje" => "La dirección no puede superar 255 caracteres."
            ];
        }

        try {
            $resultado = ModeloSedes::mdlCrearSede([
                "nombre" => $nombre,
                "direccion" => $direccion
            ]);

            if ($resultado === "ok") {
                return [
                    "estado" => "ok",
                    "mensaje" => "Sede creada correctamente."
                ];
            }
        } catch (PDOException $e) {
            if ($e->getCode() === "23000") {
                return [
                    "estado" => "error",
                    "mensaje" => "Ya existe una sede con ese nombre."
                ];
            }

            error_log("Error al crear sede: " . $e->getMessage());
        }

        return [
            "estado" => "error",
            "mensaje" => "No fue posible crear la sede."
        ];
    }

    static public function ctrActualizarSede($datos)
    {
        $id = filter_var($datos["id"] ?? null, FILTER_VALIDATE_INT);
        $nombre = trim($datos["nombre"] ?? "");
        $direccion = trim($datos["direccion"] ?? "");

        if ($id === false || $id === null || $id < 1) {
            return [
                "estado" => "error",
                "mensaje" => "El identificador de la sede no es válido."
            ];
        }

        if ($nombre === "" || mb_strlen($nombre, "UTF-8") > 120) {
            return [
                "estado" => "error",
                "mensaje" => "El nombre es obligatorio y no puede superar 120 caracteres."
            ];
        }

        if (mb_strlen($direccion, "UTF-8") > 255) {
            return [
                "estado" => "error",
                "mensaje" => "La dirección no puede superar 255 caracteres."
            ];
        }

        try {
            if (!ModeloSedes::mdlMostrarSedePorId($id)) {
                return [
                    "estado" => "error",
                    "mensaje" => "La sede seleccionada no existe."
                ];
            }

            $resultado = ModeloSedes::mdlActualizarSede([
                "id" => $id,
                "nombre" => $nombre,
                "direccion" => $direccion
            ]);

            if ($resultado === "ok") {
                return [
                    "estado" => "ok",
                    "mensaje" => "Sede actualizada correctamente."
                ];
            }
        } catch (PDOException $e) {
            if ($e->getCode() === "23000") {
                return [
                    "estado" => "error",
                    "mensaje" => "Ya existe otra sede con ese nombre."
                ];
            }

            error_log("Error al actualizar sede: " . $e->getMessage());
        }

        return [
            "estado" => "error",
            "mensaje" => "No fue posible actualizar la sede."
        ];
    }

    static public function ctrCambiarEstadoSede($datos)
    {
        $id = filter_var($datos["id"] ?? null, FILTER_VALIDATE_INT);
        $activa = filter_var($datos["activa"] ?? null, FILTER_VALIDATE_INT);

        if (
            $id === false || $id === null || $id < 1 ||
            !in_array($activa, [0, 1], true)
        ) {
            return [
                "estado" => "error",
                "mensaje" => "Los datos para cambiar el estado no son válidos."
            ];
        }

        try {
            if (!ModeloSedes::mdlMostrarSedePorId($id)) {
                return [
                    "estado" => "error",
                    "mensaje" => "La sede seleccionada no existe."
                ];
            }

            $resultado = ModeloSedes::mdlCambiarEstadoSede($id, $activa);

            if ($resultado === "ok") {
                return [
                    "estado" => "ok",
                    "mensaje" => $activa === 1
                        ? "Sede activada correctamente."
                        : "Sede desactivada correctamente. Se conservó su historial."
                ];
            }
        } catch (PDOException $e) {
            error_log("Error al cambiar estado de sede: " . $e->getMessage());
        }

        return [
            "estado" => "error",
            "mensaje" => "No fue posible cambiar el estado de la sede."
        ];
    }

    static public function ctrActualizarTurnos($datos)
    {
        $id = filter_var($datos["id"] ?? null, FILTER_VALIDATE_INT);
        $general = filter_var($datos["general"] ?? null, FILTER_VALIDATE_INT);
        $rapido = filter_var($datos["rapido"] ?? null, FILTER_VALIDATE_INT);
        $cita = filter_var($datos["cita"] ?? null, FILTER_VALIDATE_INT);

        if (
            $id === false || $id === null || $id < 1 ||
            $general === false || $general === null || $general < 0 ||
            $rapido === false || $rapido === null || $rapido < 0 ||
            $cita === false || $cita === null || $cita < 0 ||
            $general > 4294967295 ||
            $rapido > 4294967295 ||
            $cita > 4294967295
        ) {
            return [
                "estado" => "error",
                "mensaje" => "Verifica los números de turno ingresados."
            ];
        }

        try {
            $resultado = ModeloSedes::mdlActualizarTurnos([
                "id" => $id,
                "general" => $general,
                "rapido" => $rapido,
                "cita" => $cita
            ]);

            if ($resultado === "ok") {
                return [
                    "estado" => "ok",
                    "mensaje" => "Turnos actualizados correctamente."
                ];
            }

            if ($resultado === "no_disponible") {
                return [
                    "estado" => "error",
                    "mensaje" => "La sede no existe o está inactiva."
                ];
            }
        } catch (PDOException $e) {
            error_log("Error SQL al actualizar turnos: " . $e->getMessage());
        } catch (Exception $e) {
            error_log("Error al actualizar turnos: " . $e->getMessage());
        }

        return [
            "estado" => "error",
            "mensaje" => "No fue posible actualizar los turnos."
        ];
    }
}
