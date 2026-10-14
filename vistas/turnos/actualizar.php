
<?php

require_once __DIR__ . "/../../configuracion/seguridad.php";
exigirRol(["admin", "superadmin"]);

require_once __DIR__ . "/../../modelos/sede.modelo.php";
require_once __DIR__ . "/../../controladores/sede.controlador.php";

$sedeId = 1;
$mensaje = "";
$estado = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $resultado = ControladorSedes::ctrActualizarTurnos([
        "id" => $sedeId,
        "general" => $_POST["general"] ?? null,
        "rapido" => $_POST["rapido"] ?? null,
        "cita" => $_POST["cita"] ?? null
    ]);

    $estado = $resultado["estado"] ?? "error";
    $mensaje = $resultado["mensaje"] ?? "Ocurrió un error al procesar la solicitud.";
}

$sede = ModeloSedes::mdlMostrarSedePorId($sedeId);

if (!$sede) {
    http_response_code(404);
    exit("No se encontró la sede seleccionada.");
}

function escapar($valor): string
{
    return htmlspecialchars((string) $valor, ENT_QUOTES, "UTF-8");
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Actualizar turnos | Sistema de turnos</title>

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            padding: 30px 16px;
            background: #f3f5f9;
            color: #1f2937;
            font-family: Arial, Helvetica, sans-serif;
        }

        .contenedor {
            width: 100%;
            max-width: 650px;
            margin: 0 auto;
        }

        .encabezado {
            margin-bottom: 24px;
        }

        .encabezado h1 {
            margin: 0 0 8px;
            font-size: 28px;
        }

        .encabezado p {
            margin: 0;
            color: #64748b;
            line-height: 1.5;
        }

        .tarjeta {
            padding: 28px;
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 14px;
            box-shadow: 0 8px 25px rgba(15, 23, 42, 0.05);
        }

        .sede {
            margin-bottom: 24px;
            padding: 14px 16px;
            background: #f0fdf4;
            border: 1px solid #bbf7d0;
            border-radius: 9px;
        }

        .sede strong {
            display: block;
            margin-bottom: 5px;
            color: #166534;
        }

        .sede span {
            color: #475569;
            font-size: 14px;
        }

        .campo {
            margin-bottom: 22px;
        }

        .campo label {
            display: block;
            margin-bottom: 8px;
            font-weight: 700;
        }

        .campo small {
            display: block;
            margin-top: 7px;
            color: #64748b;
            line-height: 1.4;
        }

        .campo input {
            width: 100%;
            padding: 13px 14px;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            font-size: 17px;
        }

        .campo input:focus {
            outline: 2px solid #bfdbfe;
            border-color: #2563eb;
        }

        .mensaje {
            margin-bottom: 20px;
            padding: 13px 15px;
            border-radius: 8px;
            line-height: 1.5;
        }

        .mensaje.ok {
            color: #166534;
            background: #dcfce7;
        }

        .mensaje.error {
            color: #991b1b;
            background: #fee2e2;
        }

        .acciones {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            margin-top: 26px;
        }

        .boton {
            display: inline-block;
            padding: 12px 17px;
            border: 0;
            border-radius: 8px;
            font-size: 15px;
            font-weight: 700;
            text-decoration: none;
            cursor: pointer;
        }

        .boton-principal {
            color: #fff;
            background: #1d4ed8;
        }

        .boton-principal:hover {
            background: #1e40af;
        }

        .boton-secundario {
            color: #334155;
            background: #e2e8f0;
        }

        @media (max-width: 480px) {
            .tarjeta {
                padding: 20px;
            }

            .encabezado h1 {
                font-size: 24px;
            }

            .acciones .boton {
                width: 100%;
                text-align: center;
            }
        }
    </style>
</head>

<body>
    <main class="contenedor">
        <header class="encabezado">
            <h1>Actualizar turnos atendidos</h1>
            <p>
                Indica el último número que fue atendido en cada fila.
                No registres aquí la ficha del siguiente cliente.
            </p>
        </header>

        <section class="tarjeta">
            <div class="sede">
                <strong>Sede: <?= escapar($sede["nombre"]) ?></strong>
                <span>Los cambios se aplicarán únicamente a esta sede.</span>
            </div>

            <?php if ($mensaje !== ""): ?>
                <div class="mensaje <?= escapar($estado) ?>">
                    <?= escapar($mensaje) ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="">
                <div class="campo">
                    <label for="general">Turno General (G)</label>
                    <input
                        type="number"
                        id="general"
                        name="general"
                        min="0"
                        step="1"
                        required
                        value="<?= escapar($sede["turno_actual_g"]) ?>"
                    >
                    <small>Último número atendido en la fila general.</small>
                </div>

                <div class="campo">
                    <label for="rapido">Turno Rápido (R)</label>
                    <input
                        type="number"
                        id="rapido"
                        name="rapido"
                        min="0"
                        step="1"
                        required
                        value="<?= escapar($sede["turno_actual_r"]) ?>"
                    >
                    <small>Último número atendido en la fila rápida.</small>
                </div>

                <div class="campo">
                    <label for="cita">Turno Por cita (C)</label>
                    <input
                        type="number"
                        id="cita"
                        name="cita"
                        min="0"
                        step="1"
                        required
                        value="<?= escapar($sede["turno_actual_c"]) ?>"
                    >
                    <small>Último número atendido en la fila de citas.</small>
                </div>

                <div class="acciones">
                    <button type="submit" class="boton boton-principal">
                        Guardar turnos
                    </button>

                    <a
                        href="/turnos/vistas/dashboard/inicio.php"
                        class="boton boton-secundario"
                    >
                        Volver al panel
                    </a>
                </div>
            </form>
        </section>
    </main>
</body>
</html>
