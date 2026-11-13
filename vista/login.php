<?php

session_start();

if (isset($_SESSION['idusuario'])) {
    header('Location: ../index.php');
    exit;
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar sesión | Jet Express</title>

    <link rel="stylesheet" href="../assets/css/login.css">
</head>
<body>

    <div class="contenedor">
        <main class="login-layout">
            <section class="login-visual" aria-label="Envíos seguros con Jet Express">
                <a class="login-marca" href="../index.php">
                    <span class="login-marca-icono">JE</span>
                    <span><strong>JET EXPRESS</strong><small>Conectamos destinos</small></span>
                </a>
                <div class="login-mensaje">
                    <p class="login-etiqueta">LOGÍSTICA · CONFIANZA · RAPIDEZ</p>
                    <h2>Cada envío<br>llega más lejos.</h2>
                    <p>Administra guías, clientes y despachos desde un solo lugar.</p>
                </div>
                <img
                    class="login-ilustracion"
                    src="../assets/images/login-illustration.svg"
                    alt=""
                    aria-hidden="true"
                >
                <p class="login-ubicacion">Cobija, Pando · Bolivia</p>
            </section>

            <section class="panel-login">
                <div class="login-titulo">
                    <p class="login-etiqueta">BIENVENIDO DE NUEVO</p>
                    <h1>Iniciar sesión</h1>
                    <p class="subtitulo">Ingresa tus datos para continuar.</p>
                </div>

                <?php if (isset($_SESSION['mensaje_login'])): ?>
                    <p class="mensaje-login" role="alert">
                        <?= htmlspecialchars($_SESSION['mensaje_login'], ENT_QUOTES, 'UTF-8') ?>
                    </p>
                    <?php unset($_SESSION['mensaje_login']); ?>
                <?php endif; ?>

                <form action="../controlador/contLogin.php" method="POST">
                    <div class="campo">
                        <label for="usuario">Usuario</label>
                        <input
                            type="text"
                            id="usuario"
                            name="usuario"
                            placeholder="Escribe tu usuario"
                            autocomplete="username"
                            required
                        >
                    </div>

                    <div class="campo">
                        <label for="password">Contraseña</label>
                        <input
                            type="password"
                            id="password"
                            name="password"
                            placeholder="Escribe tu contraseña"
                            autocomplete="current-password"
                            required
                        >
                    </div>

                    <button type="submit">Ingresar <span aria-hidden="true">→</span></button>
                </form>

                <p class="pie">Acceso seguro · © Jet Express</p>
            </section>
        </main>
    </div>

</body>
</html>
