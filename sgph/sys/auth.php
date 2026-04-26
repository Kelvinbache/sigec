<?php
session_start();
header_remove("X-Powered-By");

require_once "conexion.php";
require_once "../sys-logs/log_manager.php";

$log = new LogManager($pdo);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $usuario = trim($_POST["usuario"]);
    $password = $_POST["password"];

    if (empty($usuario) || empty($password)) {
        header("Location: ../index.php?error=Complete todos los campos");
        exit();
    }

    try {
        $stmt = $pdo->prepare("SELECT u.*, r.nombre as rol_nombre
                               FROM usuarios u
                               JOIN roles r ON u.rol_id = r.id
                               WHERE u.nombre_usuario = :usuario AND u.estado = 'activo'");
        $stmt->execute([":usuario" => $usuario]);
        $user = $stmt->fetch();

        if ($user && $password == $user["contrasenia_hash"]) {
            $_SESSION["usuario_id"] = $user["id"];
            $_SESSION["usuario_nombre"] = $user["nombre_usuario"];
            $_SESSION["rol"] = $user["rol_nombre"];

            if (isset($_COOKIE["fecha_inicio"])) {
                setcookie("fecha_inicio", "", time() - 3600, "/");
            }
            if (isset($_COOKIE["rol"])) {
                setcookie("rol", "", time() - 3600, "/");
            }

            setcookie("fecha_inicio", date("Y-m-d H:i:s"), time() + 86400, "/");
            setcookie("rol", $_SESSION["rol"], time() + 86400, "/");

            $log->registrar(
                $user["id"],
                "INICIO_SESION",
                "Usuario {$_SESSION["nombre_usuario"]} inició sesión",
            );

            header("Location: ../php/dash.php");
            exit();
        } else {
            $log->registrar(
                0,
                "INTENTO_FALLIDO",
                "Intento con usuario: $usuario",
            );
            header(
                "Location: ../index.php?error=Usuario o contraseña incorrectos",
            );
            exit();
        }
    } catch (PDOException $e) {
        header("Location: ../index.php?error=Error en el sistema");
        exit();
    }
} else {
    header("Location: ../index.php");
    exit();
}
?>
