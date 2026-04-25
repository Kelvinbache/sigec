<?php
// index.php
session_start();
require_once __DIR__ . "/config/db.php";

$error = "";

// Ya no generamos cookie aquí, se generará SOLO al hacer login

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $usuario = trim($_POST["usuario"] ?? "");
    $password = trim($_POST["password"] ?? "");

    if (!empty($usuario) && !empty($password)) {
        $stmt = $conn->prepare(
            "SELECT id, usuario, hash, nivel FROM usuarios WHERE usuario = ?",
        );
        $stmt->bind_param("s", $usuario);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($row = $result->fetch_assoc()) {
            if (hash("sha256", $password) === $row["hash"]) {
                // REGENERAR cookie SOLO al iniciar sesión (nueva cada login)
                $cookie_id = bin2hex(random_bytes(32));

                // Guardar cookie por 30 días
                setcookie(
                    "cookie_sesion",
                    $cookie_id,
                    time() + 30 * 24 * 60 * 60,
                    "/",
                    "",
                    false,
                    true,
                );

                // Guardar datos del usuario en sesión
                $_SESSION["user_id"] = $row["id"];
                $_SESSION["usuario"] = $row["usuario"];
                $_SESSION["nivel"] = $row["nivel"];
                $_SESSION["cookie_id"] = $cookie_id;

                // Registrar login exitoso
                $ip =
                    $_SERVER["HTTP_X_FORWARDED_FOR"] ??
                    ($_SERVER["REMOTE_ADDR"] ?? "0.0.0.0");
                $user_agent = $_SERVER["HTTP_USER_AGENT"] ?? "Desconocido";

                $log_sql = "INSERT INTO logs_sistema (cookie_id, usuario_id, usuario_nombre, accion, ip_usuario, user_agent, datos_nuevos)
                           VALUES (?, ?, ?, 'LOGIN_EXITOSO', ?, ?, ?)";
                $log_stmt = $conn->prepare($log_sql);
                $detalle = json_encode([
                    "mensaje" => "Inicio de sesión exitoso",
                    "usuario" => $usuario,
                    "cookie_nueva" => substr($cookie_id, 0, 20) . "...",
                ]);
                $log_stmt->bind_param(
                    "sissss",
                    $cookie_id,
                    $row["id"],
                    $usuario,
                    $ip,
                    $user_agent,
                    $detalle,
                );
                $log_stmt->execute();
                $log_stmt->close();

                header("Location: dashboard.php");
                exit();
            } else {
                // Login fallido - usar cookie temporal o existente
                $cookie_id =
                    $_COOKIE["cookie_sesion"] ?? bin2hex(random_bytes(32));

                $ip =
                    $_SERVER["HTTP_X_FORWARDED_FOR"] ??
                    ($_SERVER["REMOTE_ADDR"] ?? "0.0.0.0");
                $user_agent = $_SERVER["HTTP_USER_AGENT"] ?? "Desconocido";

                $log_sql = "INSERT INTO logs_sistema (cookie_id, usuario_nombre, accion, ip_usuario, user_agent, datos_nuevos)
                           VALUES (?, ?, 'LOGIN_FALLIDO', ?, ?, ?)";
                $log_stmt = $conn->prepare($log_sql);
                $detalle = json_encode([
                    "mensaje" => "Intento de login fallido",
                    "usuario_intentado" => $usuario,
                ]);
                $log_stmt->bind_param(
                    "sssss",
                    $cookie_id,
                    $usuario,
                    $ip,
                    $user_agent,
                    $detalle,
                );
                $log_stmt->execute();
                $log_stmt->close();

                $error = "Contraseña incorrecta";
            }
        } else {
            // Usuario no existe
            $cookie_id = $_COOKIE["cookie_sesion"] ?? bin2hex(random_bytes(32));

            $ip =
                $_SERVER["HTTP_X_FORWARDED_FOR"] ??
                ($_SERVER["REMOTE_ADDR"] ?? "0.0.0.0");
            $user_agent = $_SERVER["HTTP_USER_AGENT"] ?? "Desconocido";

            $log_sql = "INSERT INTO logs_sistema (cookie_id, usuario_nombre, accion, ip_usuario, user_agent, datos_nuevos)
                       VALUES (?, ?, 'LOGIN_FALLIDO', ?, ?, ?)";
            $log_stmt = $conn->prepare($log_sql);
            $detalle = json_encode([
                "mensaje" => "Usuario no encontrado",
                "usuario_intentado" => $usuario,
            ]);
            $log_stmt->bind_param(
                "sssss",
                $cookie_id,
                $usuario,
                $ip,
                $user_agent,
                $detalle,
            );
            $log_stmt->execute();
            $log_stmt->close();

            $error = "Usuario no encontrado";
        }
        $stmt->close();
    } else {
        $error = "Por favor complete todos los campos";
    }
}

// Mostrar cookie actual si existe (solo para depuración)
$cookie_actual = $_COOKIE["cookie_sesion"] ?? "No hay cookie";
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - SIGEC</title>
</head>
<body>
    <div class="login-container">
        <h2>SIGEC - Iniciar Sesión</h2>
        <?php if ($error): ?>
            <div class="error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        <form method="POST" action="">
            <input type="text" name="usuario" placeholder="Usuario" required>
            <input type="password" name="password" placeholder="Contraseña" required>
            <button type="submit">Ingresar</button>
        </form>
    </div>
</body>
</html>
