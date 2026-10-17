<?php

require_once __DIR__ . "/../../controladores/turnos.controlador.php";

function escaparConsulta($valor): string
{
    return htmlspecialchars((string) $valor, ENT_QUOTES, "UTF-8");
}

// Respuesta JSON para las actualizaciones automáticas.
if (
    $_SERVER["REQUEST_METHOD"] === "POST"
    && ($_POST["accion"] ?? "") === "actualizar"
) {
    header("Content-Type: application/json; charset=UTF-8");
    header("Cache-Control: no-store, no-cache, must-revalidate");

    $resultado = ControladorTurnos::ctrConsultarTurnosPorTelefono(
        trim($_POST["telefono"] ?? "")
    );

    echo json_encode($resultado, JSON_UNESCAPED_UNICODE);
    exit;
}

$telefono = "";
$mensaje = "";
$estado = "";
$turnos = [];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $telefono = trim($_POST["telefono"] ?? "");

    $resultado = ControladorTurnos::ctrConsultarTurnosPorTelefono(
        $telefono
    );

    $estado = $resultado["estado"] ?? "error";
    $mensaje = $resultado["mensaje"] ?? "Ocurrió un error.";
    $turnos = $resultado["turnos"] ?? [];
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Consultar mi turno</title>
    <link rel="stylesheet" href="../assets/css/consultar.css">
    <script src="../assets/js/consultar.js" defer></script>
</head>

<body>
<main class="contenedor">

    <header class="encabezado">
        <h1>Consulta tu turno</h1>
        <p>
            Ingresa el celular que proporcionaste al registrar tu ficha
            para consultar el avance de hoy.
        </p>
    </header>

    <section class="tarjeta">
        <form method="POST" action="">
            <label for="telefono">Número de celular</label>

            <input
                type="tel"
                id="telefono"
                name="telefono"
                inputmode="numeric"
                pattern="[0-9]{7,15}"
                minlength="7"
                maxlength="15"
                placeholder="Ej. 3211234567"
                value="<?= escaparConsulta($telefono) ?>"
                required
            >

            <small class="ayuda">
                Escribe únicamente los números, sin espacios ni guiones.
            </small>

            <button type="submit">Consultar mis turnos</button>
        </form>
    </section>

    <div
        id="mensaje-consulta"
        class="mensaje <?= escaparConsulta($estado) ?>"
        <?= $mensaje === "" ? "hidden" : "" ?>
    ><?= escaparConsulta($mensaje) ?></div>

    <div id="resultados-turnos">
        <?php foreach ($turnos as $turno): ?>
            <?php
                $numero = (int) $turno["numero"];
                $actual = (int) $turno["turno_actual"];
                $faltan = max(0, $numero - $actual - 1);

                $situacion = $numero <= $actual
                    ? "Tu turno ya está siendo atendido o ya pasó."
                    : "Hay " . $faltan . " turno(s) delante de ti.";

                $tipos = [
                    "G" => "General",
                    "R" => "Rápido",
                    "C" => "Por cita"
                ];

                $tipoNombre = $tipos[$turno["tipo"]] ?? "Turno";
            ?>

            <section
                class="tarjeta resultado"
                data-tipo="<?= escaparConsulta($turno["tipo"]) ?>"
                data-numero="<?= $numero ?>"
            >
                
<h2>Fila <?= escaparConsulta($tipoNombre) ?></h2>

<div class="dato">
    <span>Nombre del cliente</span>
    <strong>
        <?= escaparConsulta($turno["nombre_cliente"] ?? "No disponible") ?>
    </strong>
</div>


                <div class="numero-turno">
                    <?= escaparConsulta($turno["tipo"]) ?>-<?= $numero ?>
                </div>

                <div class="dato">
                    <span>Último número atendido</span>
                    <strong class="ultimo-atendido">
                        <?= escaparConsulta($turno["tipo"]) ?>-<?= $actual ?>
                    </strong>
                </div>

                <div class="dato">
                    <span>Turnos por delante</span>
                    <strong class="turnos-delante"><?= $faltan ?></strong>
                </div>

                <p class="situacion-turno">
                    <?= escaparConsulta($situacion) ?>
                </p>
            </section>
        <?php endforeach; ?>
    </div>

    <p class="nota">
        La cantidad de turnos por delante es una estimación basada en
        los números registrados y el último turno atendido. No representa
        un tiempo de espera exacto.
    </p>

</main>
</body>
</html>