<?php

header_remove("X-Powered-By");
session_start();

if (!isset($_SESSION["usuario_id"])) {
    header("Location: ../index.php");
    exit();
}

require_once "../sys/conexion.php";

$fecha_inicio = isset($_COOKIE["fecha_inicio"])
    ? $_COOKIE["fecha_inicio"]
    : "No disponible";
$rol = isset($_COOKIE["rol"]) ? $_COOKIE["rol"] : "No disponible";

$nombre_usuario = $_SESSION["usuario_nombre"];
$es_admin = $_SESSION["rol"] == "super_usuario";

$stats = [];
$stats["habitantes"] = $pdo
    ->query("SELECT COUNT(*) FROM habitantes")
    ->fetchColumn();
$stats["viviendas"] = $pdo
    ->query("SELECT COUNT(*) FROM viviendas")
    ->fetchColumn();
$stats["diagnosticos"] = $pdo
    ->query("SELECT COUNT(*) FROM diagnosticos")
    ->fetchColumn();
$stats["discapacidades"] = $pdo
    ->query("SELECT COUNT(*) FROM discapacidades")
    ->fetchColumn();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Dashboard - SIGEC</title>
    <link rel="stylesheet" href="../css/main.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <nav class="navbar">
        <div class="navbar-brand"><img src="../img/icon.png"><h1>SIGEC</h1></div>
        <div class="user-info">
            <div class="user-details">
                <span><?php echo htmlspecialchars($nombre_usuario); ?></span>
                <small><?php echo $es_admin
                    ? "Super Usuario"
                    : "Encargado"; ?></small>
            </div>
            <div class="user-avatar"><?php echo strtoupper(
                substr($nombre_usuario, 0, 1),
            ); ?></div>
            <a href="../sys/logauth.php" class="logout-btn"><i class="fas fa-sign-out-alt"></i> Salir</a>
        </div>
    </nav>

    <div class="dashboard-content">
        <div class="actions-container" id="actionsContainer">
            <h2 class="actions-title"><i class="fas fa-tachometer-alt"></i> Panel de Control</h2>

            <div class="estadisticas-grid">
                <div class="estadistica-card">
                    <div class="estadistica-icono"><i class="fas fa-users"></i></div>
                    <span class="estadistica-numero"><?php echo $stats[
                        "habitantes"
                    ]; ?></span>
                    <span class="estadistica-texto">Personas Registradas</span>
                </div>
                <div class="estadistica-card">
                    <div class="estadistica-icono"><i class="fas fa-home"></i></div>
                    <span class="estadistica-numero"><?php echo $stats[
                        "viviendas"
                    ]; ?></span>
                    <span class="estadistica-texto">Viviendas Registradas</span>
                </div>
                <div class="estadistica-card">
                    <div class="estadistica-icono"><i class="fas fa-stethoscope"></i></div>
                    <span class="estadistica-numero"><?php echo $stats[
                        "diagnosticos"
                    ]; ?></span>
                    <span class="estadistica-texto">Diagnósticos</span>
                </div>
                <div class="estadistica-card">
                    <div class="estadistica-icono"><i class="fas fa-wheelchair"></i></div>
                    <span class="estadistica-numero"><?php echo $stats[
                        "discapacidades"
                    ]; ?></span>
                    <span class="estadistica-texto">Discapacidades</span>
                </div>
            </div>

            <div class="actions-grid">
                <a href="habs.php" class="action-card">
                    <div class="action-icon"><i class="fas fa-user-friends"></i></div>
                    <div><h3>Gestión de Personas</h3><p>Administrar habitantes, diagnósticos y discapacidades</p></div>
                </a>
                <a href="cas.php" class="action-card">
                    <div class="action-icon"><i class="fas fa-building"></i></div>
                    <div><h3>Gestión de Viviendas</h3><p>Administrar viviendas, servicios y estado</p></div>
                </a>
                <?php if ($es_admin): ?>
                <a href="../sec/adm.php" class="action-card">
                    <div class="action-icon"><i class="fas fa-user-cog"></i></div>
                    <div><h3>Gestionar Encargado <span class="admin-badge">Admin</span></h3><p>Modificar datos del encargado</p></div>
                </a>
                <a href="../sec/log.php" class="action-card">
                    <div class="action-icon"><i class="fas fa-history"></i></div>
                    <div><h3>Registro de Actividades <span class="admin-badge">Admin</span></h3><p>Ver logs del sistema</p></div>
                </a>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(() => {
                document.getElementById('actionsContainer').classList.add('loaded');
            }, 300);
        });
    </script>
</body>
</html>
