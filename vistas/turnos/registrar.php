
<?php

require_once __DIR__ . "/../../configuracion/seguridad.php";
exigirRol(["admin", "superadmin"]);

require_once __DIR__ . "/../../controladores/turnos.controlador.php";
require_once __DIR__ . "/../../modelos/sede.modelo.php";

$resultado = null;

$sedeId = $_SESSION["usuario"]["sede_id"] ?? null;
$nombreSede = "Sin sede asignada";

if ($sedeId !== null) {
    $sede = ModeloSedes::mdlMostrarSedePorId((int) $sedeId);

    if ($sede) {
        $nombreSede = $sede["nombre"];
    }
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $resultado = ControladorTurnos::ctrRegistrarTurno($_POST);
}

function escaparRegistro($valor): string
{
    return htmlspecialchars((string) $valor, ENT_QUOTES, "UTF-8");
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrar ficha | Control de turnos</title>
    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            padding: 24px 16px;
            background: #f3f6f4;
            color: #24352b;
            font-family: Arial, sans-serif;
        }

        .contenedor {
            width: 100%;
            max-width: 560px;
            margin: 30px auto;
            padding: 28px;
            background: #fff;
            border-radius: 14px;
            box-shadow: 0 8px 28px rgba(30, 60, 40, .08);
        }

        h1 {
            margin: 0 0 10px;
            font-size: 27px;
        }

        .sede {
            margin: 0 0 24px;
            padding: 13px 15px;
            background: #edf4ed;
            border-radius: 8px;
            color: #365941;
            line-height: 1.5;
        }

        label {
            display: block;
            margin: 17px 0 7px;
            font-weight: 600;
        }

        input, select {
            width: 100%;
            padding: 12px;
            border: 1px solid #d6dfd8;
            border-radius: 8px;
            font-size: 16px;
            background: #fff;
        }

        input:focus, select:focus {
            outline: 2px solid #a7c6ad;
            border-color: #65896d;
        }

        button, .boton {
            display: inline-block;
            padding: 12px 17px;
            border: 0;
            border-radius: 8px;
            background: #315d40;
            color: #fff;
            font-size: 15px;
            text-decoration: none;
            cursor: pointer;
        }

        button {
            width: 100%;
            margin-top: 23px;
        }

        .boton-secundario {
            background: #e9eee9;
            color: #31513b;
        }

        .mensaje {
            margin: 18px 0;
            padding: 13px;
            border-radius: 8px;
            line-height: 1.5;
        }

        .ok {
            background: #e8f5e9;
            color: #246337;
        }

        .error {
            background: #ffeded;
            color: #a12828;
        }

        .enlaces {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 20px;
        }

        @media (max-width: 480px) {
            .contenedor {
                padding: 21px;
            }
        }
    </style>
</head>

<body>
<main class="contenedor">

    <h1>Registrar ficha</h1>

    <p class="sede">
        Sede asignada:
        <strong><?= escaparRegistro($nombreSede) ?></strong>
    </p>

    <?php if ($resultado !== null): ?>
        <div class="mensaje <?= ($resultado["estado"] ?? "") === "ok" ? "ok" : "error" ?>">
            <?= escaparRegistro($resultado["mensaje"] ?? "") ?>

            <?php if (($resultado["estado"] ?? "") === "ok"): ?>
                <br>
                Número registrado:
                <strong>
                    <?= escaparRegistro($_POST["tipo"] ?? "") ?>-<?= (int) ($_POST["numero"] ?? 0) ?>
                </strong>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="">
        <label for="nombre">Nombre del cliente</label>
        <input
            type="text"
            id="nombre"
            name="nombre"
            maxlength="120"
            required
            value="<?= escaparRegistro($_POST["nombre"] ?? "") ?>"
        >

        <label for="telefono">Celular</label>
        <input
            type="tel"
            id="telefono"
            name="telefono"
            inputmode="numeric"
            pattern="[0-9]{7,15}"
            maxlength="15"
            required
            value="<?= escaparRegistro($_POST["telefono"] ?? "") ?>"
        >

        <label for="numero">Número de ficha física</label>
        <input
            type="number"
            id="numero"
            name="numero"
            min="1"
            step="1"
            required
            value="<?= escaparRegistro($_POST["numero"] ?? "") ?>"
        >

        <label for="tipo">Tipo de turno</label>
        <select id="tipo" name="tipo" required>
            <option value="">Selecciona el tipo</option>
            <option value="G" <?= ($_POST["tipo"] ?? "") === "G" ? "selected" : "" ?>>
                G - General
            </option>
            <option value="R" <?= ($_POST["tipo"] ?? "") === "R" ? "selected" : "" ?>>
                R - Rápido
            </option>
            <option value="C" <?= ($_POST["tipo"] ?? "") === "C" ? "selected" : "" ?>>
                C - Por cita
            </option>
        </select>

        <button type="submit">Registrar ficha</button>
    </form>

    <div class="enlaces">
        <a class="boton boton-secundario" href="listar.php">
            Ver fichas de hoy
        </a>

        <a class="boton boton-secundario" href="../dashboard/inicio.php">
            Volver al panel
        </a>
    </div>

</main>
</body>
</html>
