<?php
session_start();
require_once "conexion.php";
require_once "../sys-logs/log_manager.php";

header_remove("X-Powered-By");

$log = new LogManager($pdo);

$usuario_id = $_SESSION["usuario_id"] ?? null;

if ($usuario_id) {
    $log->registrar($usuario_id, "CIERRE_SESION", "Usuario cerró sesión");
}

$_SESSION = [];
session_destroy();

$cookies_basura = [
    "ultimo_acceso",
    "session_id",
    "usuario_nombre",
    "rol",
    "rol_usuario",
    "fecha_inicio",
    "cookie_sesion",
    "PHPSESSID",
];

foreach ($cookies_basura as $cookie) {
    if (isset($_COOKIE[$cookie])) {
        setcookie($cookie, "", time() - 3600, "/");
    }
}

header("Location: ../index.php");
exit();
?>
