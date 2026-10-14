<?php

require_once __DIR__ . "/../../configuracion/seguridad.php";
exigirRol(["superadmin"]);

require_once __DIR__ . "/../../controladores/sede.controlador.php";

$resultado = null;

function escaparSede($valor)
{
    return htmlspecialchars((string) ($valor ?? ""), ENT_QUOTES, "UTF-8");
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $accion = $_POST["accion"] ?? "";

    switch ($accion) {
        case "crear_sede":
            $resultado = ControladorSedes::ctrCrearSede($_POST);
            break;

        case "actualizar_sede":
            $resultado = ControladorSedes::ctrActualizarSede($_POST);
            break;

        case "cambiar_estado_sede":
            $resultado = ControladorSedes::ctrCambiarEstadoSede($_POST);
            break;

        case "actualizar_turnos":
            $resultado = ControladorSedes::ctrActualizarTurnos($_POST);
            break;

        default:
            $resultado = [
                "estado" => "error",
                "mensaje" => "La acción solicitada no es válida."
            ];
            break;
    }
}

$sedes = ControladorSedes::ctrMostrarSedes();

$idEditar = filter_var(
    $_GET["editar"] ?? null,
    FILTER_VALIDATE_INT
);

if ($idEditar === false || $idEditar < 1) {
    $idEditar = null;
}

$sedeEditar = null;

if ($idEditar !== null) {
    $sedeEditar = ControladorSedes::ctrMostrarSedePorId($idEditar);

    if (!$sedeEditar) {
        $idEditar = null;
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Gestión de sedes | Control de turnos</title>

    <link rel="stylesheet" href="../assets/css/sedes.css">
</head>

<body>
    <main class="contenedor">

        <header class="encabezado">
            <div>
                <span class="etiqueta-superior">ADMINISTRACIÓN</span>
                <h1>Gestión de sedes</h1>
                <p>Administra las ubicaciones, sus turnos y su disponibilidad.</p>
            </div>

            <a class="boton boton-secundario" href="../dashboard/inicio.php">
                <span aria-hidden="true">&larr;</span>
                Volver al panel
            </a>
        </header>

        <?php if ($resultado !== null): ?>
            <div
                class="mensaje <?= $resultado["estado"] === "ok" ? "mensaje-exito" : "mensaje-error" ?>"
                role="status"
            >
                <span class="mensaje-icono" aria-hidden="true">
                    <?= $resultado["estado"] === "ok" ? "✓" : "!" ?>
                </span>

                <span><?= escaparSede($resultado["mensaje"]) ?></span>
            </div>
        <?php endif; ?>

        <section class="tarjeta tarjeta-crear">
            <div class="titulo-seccion">
                <div class="icono-seccion" aria-hidden="true">+</div>

                <div>
                    <h2>Crear nueva sede</h2>
                    <p>Registra una nueva ubicación para gestionar sus turnos.</p>
                </div>
            </div>

            <form method="POST" class="formulario-crear">
                <input type="hidden" name="accion" value="crear_sede">

                <div class="campo">
                    <label for="nombre-nueva-sede">Nombre de la sede</label>
                    <input
                        type="text"
                        id="nombre-nueva-sede"
                        name="nombre"
                        maxlength="120"
                        placeholder="Ej. Sede norte"
                        required
                    >
                </div>

                <div class="campo">
                    <label for="direccion-nueva-sede">Dirección</label>
                    <input
                        type="text"
                        id="direccion-nueva-sede"
                        name="direccion"
                        maxlength="255"
                        placeholder="Dirección de la sede"
                    >
                </div>

                <div class="campo campo-boton">
                    <button type="submit" class="boton boton-primario">
                        Crear sede
                    </button>
                </div>
            </form>
        </section>

        <?php if ($sedeEditar): ?>
            <section class="tarjeta tarjeta-edicion" id="editar-sede">
                <div class="titulo-seccion">
                    <div class="icono-seccion icono-editar" aria-hidden="true">
                        ✎
                    </div>

                    <div>
                        <h2>Editar sede</h2>
                        <p>
                            Modifica los datos de
                            <strong><?= escaparSede($sedeEditar["nombre"]) ?></strong>.
                        </p>
                    </div>
                </div>

                <form method="POST" class="formulario-edicion">
                    <input type="hidden" name="accion" value="actualizar_sede">
                    <input
                        type="hidden"
                        name="id"
                        value="<?= (int) $sedeEditar["id"] ?>"
                    >

                    <div class="campo">
                        <label for="nombre-editar">Nombre de la sede</label>
                        <input
                            type="text"
                            id="nombre-editar"
                            name="nombre"
                            maxlength="120"
                            value="<?= escaparSede($sedeEditar["nombre"]) ?>"
                            required
                        >
                    </div>

                    <div class="campo">
                        <label for="direccion-editar">Dirección</label>
                        <input
                            type="text"
                            id="direccion-editar"
                            name="direccion"
                            maxlength="255"
                            value="<?= escaparSede($sedeEditar["direccion"]) ?>"
                        >
                    </div>

                    <div class="acciones-formulario">
                        <button type="submit" class="boton boton-primario">
                            Guardar cambios
                        </button>

                        <a href="listar.php" class="boton boton-secundario">
                            Cancelar
                        </a>
                    </div>
                </form>
            </section>
        <?php endif; ?>

        <section class="seccion-sedes">
            <div class="encabezado-listado">
                <div>
                    <h2>Sedes registradas</h2>
                    <p>Consulta y administra cada sede desde su tarjeta.</p>
                </div>

                <span class="contador-sedes">
                    <?= count($sedes) ?>
                    <?= count($sedes) === 1 ? "sede" : "sedes" ?>
                </span>
            </div>

            <?php if (empty($sedes)): ?>
                <div class="tarjeta estado-vacio">
                    <div class="vacio-icono" aria-hidden="true">⌂</div>
                    <h3>No hay sedes registradas</h3>
                    <p>Crea una sede utilizando el formulario superior.</p>
                </div>
            <?php else: ?>

                <div class="lista-sedes">
                    <?php foreach ($sedes as $sede): ?>
                        <?php
                        $activa = (int) $sede["activa"] === 1;
                        $esEdicion = $idEditar !== null
                            && (int) $idEditar === (int) $sede["id"];
                        ?>

                        <article class="tarjeta tarjeta-sede">
                            <div class="sede-encabezado">
                                <div class="sede-identidad">
                                    <div class="sede-icono" aria-hidden="true">
                                        ⌂
                                    </div>

                                    <div class="sede-titulos">
                                        <h3><?= escaparSede($sede["nombre"]) ?></h3>

                                        <p class="sede-direccion">
                                            <?= trim((string) ($sede["direccion"] ?? "")) !== ""
                                                ? escaparSede($sede["direccion"])
                                                : "Dirección sin configurar" ?>
                                        </p>
                                    </div>
                                </div>

                                <span class="estado-sede <?= $activa ? "estado-activa" : "estado-inactiva" ?>">
                                    <span class="estado-punto"></span>
                                    <?= $activa ? "Activa" : "Inactiva" ?>
                                </span>
                            </div>

                            <div class="resumen-turnos">
                                <div class="turno-resumen turno-general">
                                    <span class="turno-etiqueta">General</span>
                                    <strong><?= (int) $sede["turno_actual_g"] ?></strong>
                                </div>

                                <div class="turno-resumen turno-rapido">
                                    <span class="turno-etiqueta">Rápido</span>
                                    <strong><?= (int) $sede["turno_actual_r"] ?></strong>
                                </div>

                                <div class="turno-resumen turno-cita">
                                    <span class="turno-etiqueta">Citas</span>
                                    <strong><?= (int) $sede["turno_actual_c"] ?></strong>
                                </div>
                            </div>

                            <div class="acciones-sede">
                                <a
                                    class="boton boton-editar <?= $esEdicion ? "boton-seleccionado" : "" ?>"
                                    href="listar.php?editar=<?= (int) $sede["id"] ?>#editar-sede"
                                >
                                    <span aria-hidden="true">✎</span>
                                    Editar
                                </a>

                                <form
                                    method="POST"
                                    class="formulario-estado"
                                    onsubmit="return confirm('<?= $activa
                                        ? "¿Deseas desactivar esta sede? Se conservarán sus registros históricos."
                                        : "¿Deseas volver a activar esta sede?" ?>');"
                                >
                                    <input
                                        type="hidden"
                                        name="accion"
                                        value="cambiar_estado_sede"
                                    >

                                    <input
                                        type="hidden"
                                        name="id"
                                        value="<?= (int) $sede["id"] ?>"
                                    >

                                    <input
                                        type="hidden"
                                        name="activa"
                                        value="<?= $activa ? 0 : 1 ?>"
                                    >

                                    <button
                                        type="submit"
                                        class="boton <?= $activa ? "boton-peligro" : "boton-activar" ?>"
                                    >
                                        <?= $activa ? "Desactivar" : "Activar" ?>
                                    </button>
                                </form>
                            </div>

                            <div class="separador-sede"></div>

                            <details class="detalles-turnos" <?= !$activa ? " " : "" ?>>
                                <summary>
                                    <span>Administrar números de turno</span>
                                    <span class="flecha-detalles" aria-hidden="true">⌄</span>
                                </summary>

                                <?php if ($activa): ?>
                                    <form method="POST" class="formulario-turnos">
                                        <input
                                            type="hidden"
                                            name="accion"
                                            value="actualizar_turnos"
                                        >

                                        <input
                                            type="hidden"
                                            name="id"
                                            value="<?= (int) $sede["id"] ?>"
                                        >

                                        <div class="campo">
                                            <label for="general-<?= (int) $sede["id"] ?>">
                                                General
                                            </label>
                                            <input
                                                type="number"
                                                id="general-<?= (int) $sede["id"] ?>"
                                                name="general"
                                                min="0"
                                                max="4294967295"
                                                value="<?= (int) $sede["turno_actual_g"] ?>"
                                                required
                                            >
                                        </div>

                                        <div class="campo">
                                            <label for="rapido-<?= (int) $sede["id"] ?>">
                                                Rápido
                                            </label>
                                            <input
                                                type="number"
                                                id="rapido-<?= (int) $sede["id"] ?>"
                                                name="rapido"
                                                min="0"
                                                max="4294967295"
                                                value="<?= (int) $sede["turno_actual_r"] ?>"
                                                required
                                            >
                                        </div>

                                        <div class="campo">
                                            <label for="cita-<?= (int) $sede["id"] ?>">
                                                Citas
                                            </label>
                                            <input
                                                type="number"
                                                id="cita-<?= (int) $sede["id"] ?>"
                                                name="cita"
                                                min="0"
                                                max="4294967295"
                                                value="<?= (int) $sede["turno_actual_c"] ?>"
                                                required
                                            >
                                        </div>

                                        <div class="campo campo-boton">
                                            <button
                                                type="submit"
                                                class="boton boton-primario boton-guardar-turnos"
                                            >
                                                Guardar turnos
                                            </button>
                                        </div>
                                    </form>
                                <?php else: ?>
                                    <p class="aviso-inactiva">
                                        Activa esta sede para poder modificar sus números de turno.
                                    </p>
                                <?php endif; ?>
                            </details>
                        </article>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </section>

        <footer class="pie">
            <span>Control de turnos</span>
            <span>Gestión de sedes</span>
        </footer>

    </main>
</body>
</html>