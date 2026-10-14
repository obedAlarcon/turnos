
<?php

require_once __DIR__ . "/../../configuracion/seguridad.php";

exigirSesion();

$usuario = $_SESSION["usuario"];
$rol = trim((string) ($usuario["rol"] ?? ""));
$nombre = $usuario["nombre"] ?? "Usuario";

$roles = [
    "superadmin" => "Superadministrador",
    "admin" => "Administrador",
    "cliente" => "Cliente",
];
function escaparPanel($valor): string
{
    return htmlspecialchars((string) $valor, ENT_QUOTES, "UTF-8");
}

$roles = [
    "superadmin" => "Superadministrador",
    "admin" => "Administrador",
    "user" => "Usuario",
];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel | Control de turnos</title>
    <link rel="stylesheet" href="../assets/css/panel.css">
</head>
<body>
<div class="panel-layout">

    <header class="barra-superior">
        <a class="marca" href="inicio.php">
            <span class="marca-icono">CT</span>
            <span>Control de turnos</span>
        </a>

        
<div class="usuario">
    <span class="usuario-avatar">
        <?= strtoupper(substr(escaparPanel($nombre), 0, 1)) ?>
    </span>

    <div class="usuario-info">
        <strong><?= escaparPanel($nombre) ?></strong>
        <span><?= escaparPanel($roles[$rol] ?? "Usuario") ?></span>
    </div>

    <a href="../auth/logout.php" class="btn-logout" title="Cerrar sesión">
    <span class="logout-icono" aria-hidden="true">⇥</span>
    <span class="logout-texto">Cerrar sesión</span>
</a>
</div>

    </header>

    <main class="contenido">

        <section class="bienvenida">
            <span class="etiqueta">PANEL DE CONTROL</span>

            <?php if ($rol === "superadmin"): ?>
                <h1>Panel del superadministrador</h1>
                <p>Administra la plataforma, las sedes y los administradores del sistema.</p>

            <?php elseif ($rol === "admin"): ?>
                <h1>Panel del administrador</h1>
                <p>Gestiona las fichas y los turnos de atención de tu sede.</p>

            <?php else: ?>
                <h1>Consulta de turnos</h1>
                <p>Consulta el avance de tu turno de forma sencilla.</p>
            <?php endif; ?>
        </section>

        <?php if ($rol === "superadmin"): ?>

            <section class="modulos">

                <article class="tarjeta">
                    <div class="icono icono-sedes">S</div>
                    <h2>Gestión de sedes</h2>
                    <p>
                        Consulta y administra las sedes registradas
                        en la plataforma.
                    </p>
                    <a class="boton" href="../sedes/listar.php">
                        Gestionar sedes <span aria-hidden="true">→</span>
                    </a>
                </article>

                <article class="tarjeta">
                    <div class="icono">A</div>
                    <h2>Gestión de administradores</h2>
                    <p>
                        Crea administradores, asigna sus sedes
                        y controla el acceso a sus cuentas.
                    </p>
                    <a class="boton" href="../usuarios/listar.php">
                        Gestionar administradores
                        <span aria-hidden="true">→</span>
                    </a>
                </article>

            </section>

        <?php elseif ($rol === "admin"): ?>

            <section class="modulos">

                <article class="tarjeta">
                    <div class="icono">+</div>
                    <h2>Registrar fichas</h2>
                    <p>
                        Registra el número de ficha, los datos del cliente
                        y la fila correspondiente.
                    </p>
                    <a class="boton" href="../turnos/registrar.php">
                        Registrar ficha →
                    </a>
                </article>

                <article class="tarjeta">
                    <div class="icono">≡</div>
                    <h2>Fichas registradas hoy</h2>
                    <p>
                        Consulta las fichas registradas durante
                        la jornada actual.
                    </p>
                    <a class="boton" href="../turnos/listar.php">
                        Ver fichas →
                    </a>
                </article>

                <article class="tarjeta">
                    <div class="icono">↗</div>
                    <h2>Actualizar turnos</h2>
                    <p>
                        Actualiza el último número atendido de las filas
                        General, Rápida y Por cita.
                    </p>
                    <a class="boton" href="../turnos/actualizar.php">
                        Actualizar turnos →
                    </a>
                </article>

                <article class="tarjeta">
                    <div class="icono icono-sedes">S</div>
                    <h2>Gestión de sedes</h2>
                    <p>
                        La administración de sedes está reservada
                        al superadministrador.
                    </p>
                    <span class="boton boton-deshabilitado">
                        Acceso restringido
                    </span>
                </article>

            </section>

        <?php else: ?>

            <section class="modulos">

                <article class="tarjeta">
                    <div class="icono">⌕</div>
                    <h2>Consultar mi turno</h2>
                    <p>
                        Consulta el avance de tu ficha con el número
                        de celular registrado.
                    </p>
                    <a class="boton" href="../turnos/consultar.php">
                        Consultar turno →
                    </a>
                </article>

            </section>


        <?php endif; ?>


        <footer class="pie">
            <span>Control de turnos</span>
            <span>Gestión de atención por filas</span>
        </footer>

    </main>
</div>
</body>
</html>