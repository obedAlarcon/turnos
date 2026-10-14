
<?php

require_once __DIR__ . "/../../configuracion/seguridad.php";
exigirRol(["admin", "superadmin"]);

require_once __DIR__ . "/../../controladores/turnos.controlador.php";

$resultado = null;

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $resultado = ControladorTurnos::ctrRegistrarTurno($_POST);
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrar ficha | Control de turnos</title>
    <style>
        * { box-sizing: border-box; }

        body {
            margin: 0;
            padding: 30px 16px;
            background: #f4f6f8;
            color: #263238;
            font-family: Arial, sans-serif;
        }

        .contenedor {
            max-width: 560px;
            margin: 0 auto;
            padding: 28px;
            background: #fff;
            border-radius: 14px;
            box-shadow: 0 5px 24px rgba(0,0,0,.07);
        }

        h1 { margin-top: 0; font-size: 25px; }
        p { color: #607078; }

        label {
            display: block;
            margin: 18px 0 7px;
            font-weight: bold;
        }

        input, select {
            width: 100%;
            padding: 12px;
            border: 1px solid #ccd4d9;
            border-radius: 7px;
            font-size: 16px;
        }

        button {
            width: 100%;
            margin-top: 24px;
            padding: 13px;
            border: 0;
            border-radius: 7px;
            background: #245c4b;
            color: #fff;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
        }

        .mensaje {
            margin: 16px 0;
            padding: 12px;
            border-radius: 7px;
        }

        .ok { background: #e4f5e9; color: #176333; }
        .error { background: #fde8e7; color: #982b25; }

        .enlace {
            display: inline-block;
            margin-top: 18px;
            color: #245c4b;
        }
    </style>
</head>
<body>
    <main class="contenedor">
        <h1>Registrar ficha</h1>
        <p>Sede temporal: Sede principal.</p>

        <?php if ($resultado !== null): ?>
            <div class="mensaje <?= $resultado["estado"] === "ok" ? "ok" : "error" ?>">
                <?= htmlspecialchars($resultado["mensaje"], ENT_QUOTES, "UTF-8") ?>

                <?php if ($resultado["estado"] === "ok"): ?>
                    <br>
                    Número registrado:
                    <strong>
                        <?= htmlspecialchars($_POST["tipo"], ENT_QUOTES, "UTF-8") ?>-<?= (int) $_POST["numero"] ?>
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
                value="<?= htmlspecialchars($_POST["nombre"] ?? "", ENT_QUOTES, "UTF-8") ?>"
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
                value="<?= htmlspecialchars($_POST["telefono"] ?? "", ENT_QUOTES, "UTF-8") ?>"
            >

            <label for="numero">Número de ficha física</label>
            <input
                type="number"
                id="numero"
                name="numero"
                min="1"
                step="1"
                required
                value="<?= htmlspecialchars($_POST["numero"] ?? "", ENT_QUOTES, "UTF-8") ?>"
            >

            <label for="tipo">Tipo de turno</label>
            <select id="tipo" name="tipo" required>
                <option value="">Selecciona el tipo</option>
                <option value="G" <?= ($_POST["tipo"] ?? "") === "G" ? "selected" : "" ?>>G - General</option>
                <option value="R" <?= ($_POST["tipo"] ?? "") === "R" ? "selected" : "" ?>>R - Rápido</option>
                <option value="C" <?= ($_POST["tipo"] ?? "") === "C" ? "selected" : "" ?>>C - Por cita</option>
            </select>

            <button type="submit">Registrar ficha</button>
        </form>

        <a class="enlace" href="listar.php">Ver fichas registradas hoy</a>
        <br>
        <a class="enlace" href="../dashboard/inicio.php">Volver al panel</a>
    </main>
</body>
</html>
