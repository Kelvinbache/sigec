<?php
header_remove("X-Powered-By");
session_start();

if (isset($_SESSION["usuario_id"])) {
    header("Location: php/dash.php");
    exit();
}

$cookies_basura = [
    "ultimo_acceso",
    "session_id",
    "usuario_nombre",
    "rol",
    "rol_usuario",
];

foreach ($cookies_basura as $cookie) {
    if (isset($_COOKIE[$cookie])) {
        setcookie($cookie, "", time() - 3600, "/");
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="./img/icon.png">
    <title>Inicio de Sesión - SIGEC</title>
    <link rel="stylesheet" href="./css/main.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="login-body">
    <div class="contenedor-registro">
        <div class="encabezado">
            <img src="./img/icon.png" alt="Logo">
            <h2>SIGEC</h2>
            <p>Sistema de Gestión Poblacional y Habitacional</p>
        </div>

        <div class="formulario-registro">
            <?php if (isset($_GET["error"])): ?>
                <div class="mensaje error">
                    <i class="fas fa-exclamation-circle"></i>
                    <span><?php echo htmlspecialchars($_GET["error"]); ?></span>
                </div>
            <?php endif; ?>

            <form action="./sys/auth.php" method="post">
                <div class="grupo-formulario">
                    <label><i class="fas fa-user"></i> Usuario</label>
                    <input type="text" name="usuario" placeholder="Ingresa tu usuario" required>
                </div>

                <div class="grupo-formulario">
                    <label><i class="fas fa-lock"></i> Contraseña</label>
                    <input type="password" name="password" placeholder="Ingresa tu contraseña" required>
                </div>

                <button type="submit" class="boton-registro">
                    <i class="fas fa-sign-in-alt"></i> Iniciar Sesión
                </button>
            </form>
        </div>
    </div>
</body>
</html>
