<?php

require_once __DIR__ . "/../../configuracion/seguridad.php";
exigirRol(["admin", "superadmin"]);

require_once __DIR__ . "/../../controladores/turnos.controlador.php";

$turnos = ControladorTurnos::ctrListarTurnosHoy();

function escaparListado($valor): string
{
    return htmlspecialchars((string) $valor, ENT_QUOTES, "UTF-8");
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fichas de hoy | Control de turnos</title>
    <link rel="stylesheet" href="../assets/css/listar.css">
</head>

<body>
<main class="contenedor">

    <header class="encabezado">
        <div>
            <h1>Fichas registradas hoy</h1>
            <p>
                Se muestran únicamente las fichas de la jornada actual
                de la Sede principal.
            </p>
        </div>

        <a class="boton" href="registrar.php">
            + Registrar otra ficha
        </a>
    </header>

    <section class="panel">
        <div class="panel-cabecera">
            <h2>Listado de fichas</h2>

            <span class="contador">
                <?= count($turnos) ?>
                <?= count($turnos) === 1 ? "ficha" : "fichas" ?>
            </span>
        </div>

        <div class="tabla-contenedor">
            <table>
                <thead>
                    <tr>
                        <th>Ficha</th>
                        <th>Nombre</th>
                        <th>Celular</th>
                        <th>Fecha</th>
                    </tr>
                </thead>

                <tbody>
                <?php if (empty($turnos)): ?>
                    <tr>
                        <td colspan="4" class="vacio">
                            Todavía no hay fichas registradas hoy.
                            <br>
                            Puedes registrar la primera ficha con el botón superior.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($turnos as $turno): ?>
                        <tr>
                            <td>
                                <span class="ficha">
                                    <?= escaparListado($turno["tipo"]) ?>-<?= (int) $turno["numero"] ?>
                                </span>
                            </td>

                            <td class="nombre-cliente">
                                <?= escaparListado($turno["nombre_cliente"] ?? "") ?>
                            </td>

                            <td class="celular">
                                <?= escaparListado($turno["telefono_cliente"] ?? "") ?>
                            </td>

                            <td class="fecha">
                                <?= escaparListado($turno["fecha_turno"]) ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </section>

    <footer class="pie">
        <a class="boton boton-secundario" href="../dashboard/inicio.php">
            ← Volver al panel
        </a>
    </footer>

</main>
</body>
</html>