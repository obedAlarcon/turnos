
<?php

require_once __DIR__ . "/../modelos/usuarios.modelo.php";

class ControladorLogin
{
    static public function ctrLogin($datos)
    {
        $telefono = trim($datos["telefono"] ?? "");
        $password = $datos["password"] ?? "";

        if ($telefono === "" || $password === "") {
            return [
                "estado" => "error",
                "mensaje" => "Ingresa tu teléfono y contraseña."
            ];
        }

        $usuario = ModeloUsuarios::mdlBuscarPorTelefono($telefono);

        if (
            !$usuario ||
            !password_verify($password, $usuario["password"])
        ) {
            return [
                "estado" => "error",
                "mensaje" => "Teléfono o contraseña incorrectos."
            ];
        }

        if ((int) $usuario["activo"] !== 1) {
            return [
                "estado" => "error",
                "mensaje" => "Tu cuenta está desactivada."
            ];
        }

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        session_regenerate_id(true);

        $_SESSION["usuario"] = [
            "id" => (int) $usuario["id"],
            "nombre" => $usuario["nombre"],
            "telefono" => $usuario["telefono"],
            "rol" => $usuario["rol"],
            "sede_id" => $usuario["sede_id"] !== null
                ? (int) $usuario["sede_id"]
                : null
        ];

        return [
            "estado" => "ok",
            "mensaje" => "Inicio de sesión correcto.",
            "rol" => $usuario["rol"]
        ];
    }
}
