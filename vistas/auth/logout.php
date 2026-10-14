
<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Vaciar los datos de la sesión.
$_SESSION = [];

// Eliminar la cookie de sesión del navegador.
if (ini_get("session.use_cookies")) {
    $parametros = session_get_cookie_params();

    setcookie(
        session_name(),
        "",
        [
            "expires" => time() - 42000,
            "path" => $parametros["path"],
            "domain" => $parametros["domain"],
            "secure" => $parametros["secure"],
            "httponly" => $parametros["httponly"],
            "samesite" => $parametros["samesite"] ?? "Lax"
        ]
    );
}
// Destruir la sesión actual.
session_destroy();

// Volver al formulario de inicio de sesión.
header("Location: login.php");
exit;