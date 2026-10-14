
<?php

require_once __DIR__ . "/controladores/usuarios.controlador.php";

$resultado = null;

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $resultado = ControladorUsuarios::ctrCrearUsuario($_POST);
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de prueba</title>
</head>
<body>
    <h1>Registrar usuario de prueba</h1>

    <?php if ($resultado): ?>
        <p>
            <?= htmlspecialchars($resultado["mensaje"], ENT_QUOTES, "UTF-8") ?>
        </p>
    <?php endif; ?>

    <form method="POST">
        <label>Nombre</label><br>
        <input type="text" name="nombre" required><br><br>

        <label>Teléfono</label><br>
        <input type="tel" name="telefono" pattern="[0-9]{7,15}" required><br><br>

        <label>Contraseña</label><br>
        <input type="password" name="password" minlength="8" required><br><br>

        <button type="submit">Registrar usuario</button>
    </form>
</body>
</html>
