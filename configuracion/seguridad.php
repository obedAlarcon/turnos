
<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . "/conexion.php";

/**
 * Comprueba que exista una sesión y que el usuario siga activo.
 */
function exigirSesion(): void
{
    if (
        !isset($_SESSION["usuario"]) ||
        !isset($_SESSION["usuario"]["id"])
    ) {
        header("Location: /turnos/vistas/auth/login.php");
        exit;
    }

    try {
        $conexion = Conexion::conectar();

        $stmt = $conexion->prepare(
            "SELECT id, nombre, telefono, rol, sede_id, activo
             FROM users
             WHERE id = :id
             LIMIT 1"
        );

        $stmt->bindValue(
            ":id",
            (int) $_SESSION["usuario"]["id"],
            PDO::PARAM_INT
        );

        $stmt->execute();

        $usuarioActual = $stmt->fetch(PDO::FETCH_ASSOC);

        // La cuenta no existe o fue desactivada.
        if (!$usuarioActual || (int) $usuarioActual["activo"] !== 1) {
            $_SESSION = [];

            if (ini_get("session.use_cookies")) {
                $parametros = session_get_cookie_params();

                setcookie(
                    session_name(),
                    "",
                    time() - 42000,
                    $parametros["path"],
                    $parametros["domain"],
                    $parametros["secure"],
                    $parametros["httponly"]
                );
            }

            session_destroy();

            header("Location: /turnos/vistas/auth/login.php");
            exit;
        }

        // Sincroniza los datos actuales con la sesión.
        $_SESSION["usuario"] = [
            "id" => (int) $usuarioActual["id"],
            "nombre" => $usuarioActual["nombre"],
            "telefono" => $usuarioActual["telefono"],
            "rol" => $usuarioActual["rol"],
            "sede_id" => $usuarioActual["sede_id"] !== null
                ? (int) $usuarioActual["sede_id"]
                : null
        ];
    } catch (Throwable $e) {
        // Si no se puede comprobar el estado, no se permite continuar.
        http_response_code(503);
        exit("No se pudo verificar tu sesión. Intenta nuevamente.");
    }
}

/**
 * Comprueba que el usuario tenga uno de los roles permitidos.
 */
function exigirRol(array $rolesPermitidos): void
{
    exigirSesion();

    $rol = trim((string) ($_SESSION["usuario"]["rol"] ?? ""));

    if (!in_array($rol, $rolesPermitidos, true)) {
        http_response_code(403);
        exit("No tienes permisos para acceder a esta página.");
    }
}