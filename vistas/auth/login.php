
<?php

require_once __DIR__ . "/../../controladores/login.controlador.php";

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$resultado = null;

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $resultado = ControladorLogin::ctrLogin($_POST);

    if (($resultado["estado"] ?? "") === "ok") {
        header("Location: ../dashboard/inicio.php");
        exit;
    }
}

function escaparLogin($valor): string
{
    return htmlspecialchars((string) $valor, ENT_QUOTES, "UTF-8");
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Iniciar sesión | Control de turnos</title>

    <link rel="stylesheet" href="../assets/css/login.css">
</head>
<body>

<div class="login-page">

    <!-- Panel de identidad -->
    <aside class="login-brand-panel">

        <div class="login-brand">
            <div class="login-brand-icon">CT</div>

            <div>
                <p class="login-brand-name">Control de turnos</p>
                <p class="login-brand-description">
                    Gestión sencilla y organizada
                </p>
            </div>
        </div>

        <div class="login-brand-content">

            <div class="login-eyebrow">
                Plataforma de gestión
            </div>

            <h1>
                Organiza tus turnos.
                <span>Optimiza tu tiempo.</span>
            </h1>

            <p>
                Accede a tu espacio de trabajo y administra la información
                de forma segura, clara y organizada.
            </p>

            <div class="login-benefits">

                <div class="login-benefit">
                    <span class="login-benefit-icon">✓</span>
                    <span>Acceso seguro a tu cuenta</span>
                </div>

                <div class="login-benefit">
                    <span class="login-benefit-icon">✓</span>
                    <span>Información organizada en un solo lugar</span>
                </div>

                <div class="login-benefit">
                    <span class="login-benefit-icon">✓</span>
                    <span>Herramientas según tu perfil de usuario</span>
                </div>

            </div>
        </div>

        <div class="login-brand-footer">
            <span>Control de turnos</span>
            <span class="login-security">Acceso privado y seguro</span>
        </div>

    </aside>

    <!-- Formulario de acceso -->
    <main class="login-form-panel">

        <section class="login-card">

            <div class="login-mobile-brand">
                <div class="login-brand-icon">CT</div>

                <div>
                    <p class="login-brand-name">Control de turnos</p>
                    <p class="login-brand-description">
                        Gestión sencilla y organizada
                    </p>
                </div>
            </div>

            <header class="login-heading">
                <p class="login-small-title">Bienvenido de nuevo</p>
                <h2>Iniciar sesión</h2>
                <p>
                    Ingresa tus credenciales para acceder a la plataforma.
                </p>
            </header>

            <?php if ($resultado): ?>
                <div class="login-alert" role="alert">
                    <span>!</span>
                    <span>
                        <?= escaparLogin($resultado["mensaje"] ?? "No fue posible iniciar sesión.") ?>
                    </span>
                </div>
            <?php endif; ?>

            <form class="login-form" method="POST" action="">

                <div class="login-field">
                    <label for="telefono">Teléfono</label>

                    <div class="login-input-wrap">
                        <span class="login-input-icon" aria-hidden="true">☎</span>

                        <input
                            class="login-input"
                            type="tel"
                            id="telefono"
                            name="telefono"
                            placeholder="Ingresa tu número de teléfono"
                            pattern="[0-9]{7,15}"
                            minlength="7"
                            maxlength="15"
                            autocomplete="tel"
                            inputmode="numeric"
                            required
                        >
                    </div>

                    <p class="login-field-help">
                        Utiliza el número registrado en tu cuenta.
                    </p>
                </div>

                <div class="login-field">
                    <label for="password">Contraseña</label>

                    <div class="login-input-wrap">
                        <span class="login-input-icon" aria-hidden="true">◆</span>

                        <input
                            class="login-input password-input"
                            type="password"
                            id="password"
                            name="password"
                            placeholder="Ingresa tu contraseña"
                            autocomplete="current-password"
                            required
                        >

                        <button
                            class="login-toggle-password"
                            type="button"
                            id="togglePassword"
                            aria-label="Mostrar contraseña"
                            aria-pressed="false"
                        >
                            Ver
                        </button>
                    </div>
                </div>

                <button class="login-submit" type="submit">
                    <span>Ingresar a mi cuenta</span>
                    <span class="login-submit-arrow" aria-hidden="true">→</span>
                </button>

            </form>

            <footer class="login-form-footer">
                <p>
                    Acceso exclusivo para usuarios autorizados.
                </p>
                <p>
                    <strong>Control de turnos</strong>
                </p>
            </footer>

        </section>

    </main>

</div>

<script>
    const togglePassword = document.getElementById("togglePassword");
    const passwordInput = document.getElementById("password");

    togglePassword.addEventListener("click", function () {
        const mostrar = passwordInput.type === "password";

        passwordInput.type = mostrar ? "text" : "password";
        togglePassword.textContent = mostrar ? "Ocultar" : "Ver";
        togglePassword.setAttribute("aria-pressed", String(mostrar));
        togglePassword.setAttribute(
            "aria-label",
            mostrar ? "Ocultar contraseña" : "Mostrar contraseña"
        );
    });
</script>

</body>
</html>
