
<?php

require_once __DIR__ . "/../../configuracion/seguridad.php";

exigirRol(["superadmin"]);

require_once __DIR__ . "/../../controladores/usuarios.controlador.php";
require_once __DIR__ . "/../../controladores/sede.controlador.php";

if (empty($_SESSION["token_usuarios"])) {
    $_SESSION["token_usuarios"] = bin2hex(random_bytes(32));
}

$token = $_SESSION["token_usuarios"];
$resultado = null;

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $tokenRecibido = $_POST["token"] ?? "";

    if (
        !is_string($tokenRecibido) ||
        !hash_equals($token, $tokenRecibido)
    ) {
        http_response_code(403);
        exit("La solicitud no es válida. Recarga la página e inténtalo nuevamente.");
    }

    $accion = $_POST["accion"] ?? "";

    if ($accion === "crear_admin") {
        $resultado = ControladorUsuarios::ctrCrearAdministrador($_POST);
    } elseif ($accion === "cambiar_estado") {
        $resultado = ControladorUsuarios::ctrCambiarEstadoAdministrador($_POST);
    } elseif ($accion === "cambiar_sede") {
        $resultado = ControladorUsuarios::ctrCambiarSedeAdministrador($_POST);
    } else {
        $resultado = [
            "estado" => "error",
            "mensaje" => "La acción solicitada no es válida."
        ];
    }
}

$administradores = ControladorUsuarios::ctrListarAdministradores();
$todasLasSedes = ControladorSedes::ctrMostrarSedes();

$sedesActivas = array_values(array_filter(
    $todasLasSedes,
    static function ($sede) {
        return (int) ($sede["activa"] ?? 0) === 1;
    }
));

function escaparUsuario($valor): string
{
    return htmlspecialchars((string) $valor, ENT_QUOTES, "UTF-8");
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Administradores | Control de turnos</title>

    <link rel="stylesheet" href="../assets/css/usuarios.css">
</head>
<body>

<main>

    <header class="encabezado">
        <h1>Gestión de administradores</h1>

        <p class="descripcion">
            Crea cuentas, asigna sedes activas y controla el acceso
            sin eliminar el historial de los administradores.
        </p>

        <a class="boton boton-secundario" href="../dashboard/inicio.php">
            ← Volver al panel
        </a>
    </header>

    <?php if ($resultado): ?>
        <div class="mensaje <?= ($resultado["estado"] ?? "") === "error" ? "error" : "" ?>">
            <?= escaparUsuario($resultado["mensaje"] ?? "Operación procesada.") ?>
        </div>
    <?php endif; ?>

    <section class="tarjeta">
        <h2>Crear administrador</h2>

        <?php if (empty($sedesActivas)): ?>

            <p>
                No hay sedes activas. Activa o crea una sede antes de
                registrar un administrador.
            </p>

        <?php else: ?>

            <form method="POST" autocomplete="off">
                <input
                    type="hidden"
                    name="token"
                    value="<?= escaparUsuario($token) ?>"
                >

                <input type="hidden" name="accion" value="crear_admin">

                <div class="formulario">

                    <div>
                        <label for="nombre">Nombre completo</label>

                        <input
                            type="text"
                            id="nombre"
                            name="nombre"
                            maxlength="120"
                            required
                        >
                    </div>

                    <div>
                        <label for="telefono">Teléfono</label>

                        <input
                            type="tel"
                            id="telefono"
                            name="telefono"
                            inputmode="numeric"
                            pattern="[0-9]{7,15}"
                            maxlength="15"
                            required
                        >
                    </div>

                    <div>
                        <label for="password">Contraseña inicial</label>

                        <input
                            type="password"
                            id="password"
                            name="password"
                            minlength="8"
                            autocomplete="new-password"
                            required
                        >
                    </div>

                    <div>
                        <label for="sede_id">Sede asignada</label>

                        <select id="sede_id" name="sede_id" required>
                            <option value="">Selecciona una sede</option>

                            <?php foreach ($sedesActivas as $sede): ?>
                                <option value="<?= (int) $sede["id"] ?>">
                                    <?= escaparUsuario($sede["nombre"]) ?>
                                </option>
                            <?php endforeach; ?>

                        </select>
                    </div>

                </div>

                <p>
                    <button type="submit">Crear administrador</button>
                </p>
            </form>

        <?php endif; ?>
    </section>

    <section class="tarjeta">
        <h2>Administradores registrados</h2>

        <?php if (empty($administradores)): ?>

            <p>Todavía no hay administradores registrados.</p>

        <?php else: ?>

            <div class="tabla-contenedor">
                <table>
                    <thead>
                        <tr>
                            <th>Administrador</th>
                            <th>Sede asignada</th>
                            <th>Estado</th>
                            <th>Cambiar sede</th>
                            <th>Acceso</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php foreach ($administradores as $admin): ?>
                            <tr>

                                <td>
                                    <strong>
                                        <?= escaparUsuario($admin["nombre"]) ?>
                                    </strong>
                                    <br>

                                    <?= escaparUsuario($admin["telefono"]) ?>
                                    <br>

                                    <small>
                                        Registro:
                                        <?= escaparUsuario($admin["creado_en"]) ?>
                                    </small>
                                </td>

                                <td>
                                    <?= escaparUsuario($admin["nombre_sede"] ?? "Sin sede") ?>
                                </td>

                                <td>
                                    <?php if ((int) $admin["activo"] === 1): ?>
                                        <span class="estado activo">Activo</span>
                                    <?php else: ?>
                                        <span class="estado inactivo">Inactivo</span>
                                    <?php endif; ?>
                                </td>

                                <td>
                                    <?php if (!empty($sedesActivas)): ?>

                                        <form method="POST">
                                            <input
                                                type="hidden"
                                                name="token"
                                                value="<?= escaparUsuario($token) ?>"
                                            >

                                            <input
                                                type="hidden"
                                                name="accion"
                                                value="cambiar_sede"
                                            >

                                            <input
                                                type="hidden"
                                                name="id"
                                                value="<?= (int) $admin["id"] ?>"
                                            >

                                            <label for="sede_admin_<?= (int) $admin["id"] ?>">
                                                Nueva sede
                                            </label>

                                            <select
                                                id="sede_admin_<?= (int) $admin["id"] ?>"
                                                name="sede_id"
                                                required
                                            >
                                                <?php foreach ($sedesActivas as $sede): ?>
                                                    <option
                                                        value="<?= (int) $sede["id"] ?>"
                                                        <?= (int) $admin["sede_id"] === (int) $sede["id"] ? "selected" : "" ?>
                                                    >
                                                        <?= escaparUsuario($sede["nombre"]) ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>

                                            <button type="submit">
                                                Guardar sede
                                            </button>
                                        </form>

                                    <?php else: ?>

                                        No hay sedes activas.

                                    <?php endif; ?>
                                </td>

                                <td>
                                    <form method="POST">
                                        <input
                                            type="hidden"
                                            name="token"
                                            value="<?= escaparUsuario($token) ?>"
                                        >

                                        <input
                                            type="hidden"
                                            name="accion"
                                            value="cambiar_estado"
                                        >

                                        <input
                                            type="hidden"
                                            name="id"
                                            value="<?= (int) $admin["id"] ?>"
                                        >

                                        <?php if ((int) $admin["activo"] === 1): ?>

                                            <input
                                                type="hidden"
                                                name="activo"
                                                value="0"
                                            >

                                            <button
                                                type="submit"
                                                class="boton-peligro"
                                                onclick="return confirm('¿Deseas desactivar esta cuenta?');"
                                            >
                                                Desactivar
                                            </button>

                                        <?php else: ?>

                                            <input
                                                type="hidden"
                                                name="activo"
                                                value="1"
                                            >

                                            <button
                                                type="submit"
                                                class="boton-exito"
                                                onclick="return confirm('¿Deseas activar esta cuenta?');"
                                            >
                                                Activar
                                            </button>

                                        <?php endif; ?>
                                    </form>
                                </td>

                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

        <?php endif; ?>
    </section>

</main>

</body>
</html>
