
<?php

require_once __DIR__ . "/../modelos/usuarios.modelo.php";
require_once __DIR__ . "/../modelos/sede.modelo.php";

class ControladorUsuarios
{
    /**
     * Registro público de clientes.
     */
    static public function ctrCrearUsuario($datos)
    {
        $nombre = trim($datos["nombre"] ?? "");
        $telefono = trim($datos["telefono"] ?? "");
        $password = $datos["password"] ?? "";

        if ($nombre === "" || $telefono === "" || $password === "") {
            return [
                "estado" => "error",
                "mensaje" => "Todos los campos son obligatorios."
            ];
        }

        if (mb_strlen($nombre) > 120) {
            return [
                "estado" => "error",
                "mensaje" => "El nombre no puede superar 120 caracteres."
            ];
        }

        if (!preg_match('/^[0-9]{7,15}$/', $telefono)) {
            return [
                "estado" => "error",
                "mensaje" => "Ingresa un teléfono válido, solo con números."
            ];
        }

        if (strlen($password) < 8) {
            return [
                "estado" => "error",
                "mensaje" => "La contraseña debe tener al menos 8 caracteres."
            ];
        }

        $usuarioExistente = ModeloUsuarios::mdlMostrarUsuarios(
            "users",
            "telefono",
            $telefono
        );

        if ($usuarioExistente) {
            return [
                "estado" => "error",
                "mensaje" => "Este teléfono ya está registrado."
            ];
        }

        try {
            $registro = ModeloUsuarios::mdlCrearUsuario(
                "users",
                [
                    "nombre" => $nombre,
                    "telefono" => $telefono,
                    "password" => password_hash($password, PASSWORD_DEFAULT),
                    "rol" => "cliente",
                    "sede_id" => null
                ]
            );

            if ($registro === "ok") {
                return [
                    "estado" => "ok",
                    "mensaje" => "Usuario registrado correctamente."
                ];
            }
        } catch (PDOException $e) {
            error_log("Error al registrar cliente: " . $e->getMessage());
        }

        return [
            "estado" => "error",
            "mensaje" => "No fue posible registrar el usuario."
        ];
    }

    /**
     * Lista exclusivamente los administradores.
     */
    static public function ctrListarAdministradores()
    {
        return ModeloUsuarios::mdlListarAdministradores();
    }

    /**
     * Crea una cuenta de administrador asignada a una sede activa.
     */
    static public function ctrCrearAdministrador($datos)
    {
        $nombre = trim($datos["nombre"] ?? "");
        $telefono = trim($datos["telefono"] ?? "");
        $password = $datos["password"] ?? "";

        $sedeId = filter_var(
            $datos["sede_id"] ?? null,
            FILTER_VALIDATE_INT
        );

        if (
            $nombre === "" ||
            $telefono === "" ||
            $password === "" ||
            $sedeId === false ||
            $sedeId === null ||
            $sedeId < 1
        ) {
            return [
                "estado" => "error",
                "mensaje" => "Completa todos los campos y selecciona una sede."
            ];
        }

        if (mb_strlen($nombre) > 120) {
            return [
                "estado" => "error",
                "mensaje" => "El nombre no puede superar 120 caracteres."
            ];
        }

        if (!preg_match('/^[0-9]{7,15}$/', $telefono)) {
            return [
                "estado" => "error",
                "mensaje" => "Ingresa un teléfono válido, solo con números."
            ];
        }

        if (strlen($password) < 8) {
            return [
                "estado" => "error",
                "mensaje" => "La contraseña debe tener al menos 8 caracteres."
            ];
        }

        $sede = ModeloSedes::mdlMostrarSedePorId($sedeId);

        if (!$sede || (int) $sede["activa"] !== 1) {
            return [
                "estado" => "error",
                "mensaje" => "Selecciona una sede que esté activa."
            ];
        }

        $usuarioExistente = ModeloUsuarios::mdlMostrarUsuarios(
            "users",
            "telefono",
            $telefono
        );

        if ($usuarioExistente) {
            return [
                "estado" => "error",
                "mensaje" => "Este teléfono ya está registrado."
            ];
        }

        try {
            $registro = ModeloUsuarios::mdlCrearUsuario(
                "users",
                [
                    "nombre" => $nombre,
                    "telefono" => $telefono,
                    "password" => password_hash($password, PASSWORD_DEFAULT),
                    "rol" => "admin",
                    "sede_id" => $sedeId
                ]
            );

            if ($registro === "ok") {
                return [
                    "estado" => "ok",
                    "mensaje" => "Administrador creado y asignado a la sede correctamente."
                ];
            }
        } catch (PDOException $e) {
            if ($e->getCode() === "23000") {
                return [
                    "estado" => "error",
                    "mensaje" => "El teléfono ya existe o la sede seleccionada no es válida."
                ];
            }

            error_log("Error al crear administrador: " . $e->getMessage());
        }

        return [
            "estado" => "error",
            "mensaje" => "No fue posible crear el administrador."
        ];
    }

    /**
     * Activa o desactiva una cuenta de administrador.
     */
    static public function ctrCambiarEstadoAdministrador($datos)
    {
        $id = filter_var(
            $datos["id"] ?? null,
            FILTER_VALIDATE_INT
        );

        $activo = filter_var(
            $datos["activo"] ?? null,
            FILTER_VALIDATE_INT
        );

        if (
            $id === false || $id === null || $id < 1 ||
            $activo === false || !in_array($activo, [0, 1], true)
        ) {
            return [
                "estado" => "error",
                "mensaje" => "Los datos para cambiar el estado no son válidos."
            ];
        }

        $resultado = ModeloUsuarios::mdlActualizarEstado($id, $activo);

        if ($resultado === "ok") {
            return [
                "estado" => "ok",
                "mensaje" => $activo === 1
                    ? "Administrador activado correctamente."
                    : "Administrador desactivado correctamente."
            ];
        }

        return [
            "estado" => "error",
            "mensaje" => "No fue posible cambiar el estado del administrador."
        ];
    }

    /**
     * Cambia la sede asignada a un administrador.
     */
    static public function ctrCambiarSedeAdministrador($datos)
    {
        $id = filter_var(
            $datos["id"] ?? null,
            FILTER_VALIDATE_INT
        );

        $sedeId = filter_var(
            $datos["sede_id"] ?? null,
            FILTER_VALIDATE_INT
        );

        if (
            $id === false || $id === null || $id < 1 ||
            $sedeId === false || $sedeId === null || $sedeId < 1
        ) {
            return [
                "estado" => "error",
                "mensaje" => "Selecciona un administrador y una sede válidos."
            ];
        }

        $sede = ModeloSedes::mdlMostrarSedePorId($sedeId);

        if (!$sede || (int) $sede["activa"] !== 1) {
            return [
                "estado" => "error",
                "mensaje" => "Solo puedes asignar sedes activas."
            ];
        }

        $resultado = ModeloUsuarios::mdlActualizarSedeAdministrador(
            $id,
            $sedeId
        );

        if ($resultado === "ok") {
            return [
                "estado" => "ok",
                "mensaje" => "Sede del administrador actualizada correctamente."
            ];
        }

        return [
            "estado" => "error",
            "mensaje" => "No fue posible actualizar la sede."
        ];
    }
}
