<?php

require_once "config/db.php";

session_start();

$error = "";

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
                $_SESSION["user_id"] = $row["id"];
                $_SESSION["usuario"] = $row["usuario"];
                $_SESSION["nivel"] = $row["nivel"];
                header("Location: dashboard.php");
                exit();
            } else {
                $error = "Contraseña incorrecta";
            }
        } else {
            $error = "Usuario no encontrado";
        }
        $stmt->close();
    } else {
        $error = "Por favor complete todos los campos";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/style.css">
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
        <div style="text-align: center; margin-top: 20px; font-size: 12px; color: #666;">
            <p>Usuarios de prueba:</p>
            <p>admin / admin123 (Superusuario)</p>
            <p>usuario / usuario123 (Usuario normal)</p>
        </div>
    </div>
</body>
</html>
