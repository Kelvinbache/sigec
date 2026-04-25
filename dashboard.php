<?php
// dashboard.php
session_start();

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION["user_id"])) {
    header("Location: index.php");
    exit();
}

require_once __DIR__ . "/config/db.php";

// Recuperar el nombre de usuario de la sesión
$nombre_usuario = $_SESSION["usuario"] ?? "Invitado";
$nivel_usuario = $_SESSION["nivel"] ?? 2;
$rol = $nivel_usuario == 1 ? "Administrador" : "Usuario Normal";
$cookie_id =
    $_SESSION["cookie_id"] ?? ($_COOKIE["cookie_sesion"] ?? "No disponible");

// Registrar el ingreso al dashboard en la tabla de logs
$ip =
    $_SERVER["HTTP_X_FORWARDED_FOR"] ?? ($_SERVER["REMOTE_ADDR"] ?? "0.0.0.0");
$user_agent = $_SERVER["HTTP_USER_AGENT"] ?? "Desconocido";

$log_sql = "INSERT INTO logs_sistema (cookie_id, usuario_id, usuario_nombre, accion, ip_usuario, user_agent, datos_nuevos)
           VALUES (?, ?, ?, 'ACCESO_DASHBOARD', ?, ?, ?)";
$log_stmt = $conn->prepare($log_sql);
$detalle = json_encode([
    "mensaje" => "Usuario accedió al dashboard",
    "pagina" => "dashboard.php",
    "fecha_acceso" => date("Y-m-d H:i:s"),
]);
$log_stmt->bind_param(
    "sissss",
    $cookie_id,
    $_SESSION["user_id"],
    $nombre_usuario,
    $ip,
    $user_agent,
    $detalle,
);
$log_stmt->execute();
$log_stmt->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/style.css">
    <title>Dashboard - SIGEC</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: Arial, sans-serif;
            background: #f4f4f4;
        }
        header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        .user-info {
            text-align: right;
        }
        .user-name {
            font-size: 18px;
            font-weight: bold;
        }
        .user-role {
            font-size: 12px;
            opacity: 0.9;
        }
        .menu {
            background: #333;
            padding: 10px;
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }
        .menu a {
            color: white;
            text-decoration: none;
            padding: 10px 20px;
            background: #555;
            border-radius: 5px;
            transition: background 0.3s;
        }
        .menu a:hover {
            background: #667eea;
        }
        .container {
            padding: 20px;
        }
        .welcome-card {
            background: white;
            padding: 30px;
            border-radius: 10px;
            margin-bottom: 20px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            text-align: center;
        }
        .welcome-card h1 {
            color: #667eea;
            margin-bottom: 10px;
        }
        .welcome-message {
            font-size: 18px;
            color: #555;
            margin-top: 10px;
        }
        .stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }
        .card {
            background: white;
            padding: 25px;
            border-radius: 10px;
            text-align: center;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            transition: transform 0.3s;
        }
        .card:hover {
            transform: translateY(-5px);
        }
        .card h3 {
            color: #667eea;
            font-size: 2.5em;
            margin-bottom: 10px;
        }
        .card p {
            color: #666;
            font-size: 14px;
        }
        .logout {
            background: #e74c3c;
            padding: 8px 15px;
            border-radius: 5px;
            color: white;
            text-decoration: none;
            margin-left: 15px;
        }
        .logout:hover {
            background: #c0392b;
        }
        .fecha {
            margin-top: 20px;
            color: #666;
            font-size: 14px;
        }
        .cookie-info {
            margin-top: 20px;
            padding: 10px;
            background: #f0f0f0;
            border-radius: 5px;
            font-size: 11px;
            color: #666;
            word-break: break-all;
        }
        .log-info {
            margin-top: 10px;
            padding: 8px;
            background: #e8f5e9;
            border-radius: 5px;
            font-size: 11px;
            color: #2e7d32;
        }
    </style>
</head>
<body>
    <header>
        <div>
            <h1>🏠 SIGEC</h1>
            <small>Sistema Integral de Gestión de Comunidades</small>
        </div>
        <div class="user-info">
            <div class="user-name">
                👋 ¡Bienvenido, <?php echo htmlspecialchars(
                    $nombre_usuario,
                ); ?>!
            </div>
            <div class="user-role">
                📌 Rol: <?php echo $rol; ?> | 👤 Usuario: <?php echo htmlspecialchars(
     $nombre_usuario,
 ); ?>
            </div>
            <a href="logout.php" class="logout">Cerrar Sesión</a>
        </div>
    </header>

    <div class="menu">
        <a href="viviendas.php">🏘️ Viviendas</a>
        <a href="habitantes.php">👥 Habitantes</a>
        <a href="discapacidades.php">♿ Discapacidades</a>
        <a href="diagnosticos.php">💊 Diagnósticos</a>
        <?php if ($_SESSION["nivel"] == 1): ?>
            <a href="logs.php">📋 Logs del Sistema</a>
            <a href="usuarios.php">👤 Usuarios</a>
        <?php endif; ?>
    </div>

    <div class="container">
        <div class="welcome-card">
            <h1>📊 Panel de Control</h1>
            <div class="welcome-message">
                <?php
                $hora = date("H");
                if ($hora < 12) {
                    echo "🌅 Buenos días, ";
                } elseif ($hora < 18) {
                    echo "☀️ Buenas tardes, ";
                } else {
                    echo "🌙 Buenas noches, ";
                }
                echo htmlspecialchars($nombre_usuario) . "!";
                ?>
            </div>
            <p>Bienvenido al Sistema de Gestión de Comunidades</p>
            <div class="fecha">
                📅 <?php echo date('l, d \d\e F \d\e Y - h:i A'); ?>
            </div>
            <div class="log-info">
                ✅ Se ha registrado tu ingreso al sistema (Log guardado)
            </div>
            <div class="cookie-info">
                <strong>🍪 Cookie ID:</strong> <?php echo substr(
                    $cookie_id,
                    0,
                    40,
                ) . "..."; ?>
            </div>
        </div>

        <div class="stats">
            <?php
            // Contar viviendas
            $result = $conn->query("SELECT COUNT(*) as total FROM viviendas");
            $viviendas = $result->fetch_assoc()["total"];

            // Contar habitantes
            $result = $conn->query("SELECT COUNT(*) as total FROM habitantes");
            $habitantes = $result->fetch_assoc()["total"];

            // Contar discapacidades
            $result = $conn->query(
                "SELECT COUNT(*) as total FROM discapacidades",
            );
            $discapacidades = $result->fetch_assoc()["total"];

            // Contar diagnósticos
            $result = $conn->query(
                "SELECT COUNT(*) as total FROM diagnosticos",
            );
            $diagnosticos = $result->fetch_assoc()["total"];

            // Contar logs de hoy
            $result = $conn->query(
                "SELECT COUNT(*) as total FROM logs_sistema WHERE DATE(fecha_hora) = CURDATE()",
            );
            $logs_hoy = $result->fetch_assoc()["total"];
            ?>

            <div class="card">
                <h3><?php echo $viviendas; ?></h3>
                <p>🏘️ Viviendas Registradas</p>
            </div>
            <div class="card">
                <h3><?php echo $habitantes; ?></h3>
                <p>👥 Habitantes Registrados</p>
            </div>
            <div class="card">
                <h3><?php echo $discapacidades; ?></h3>
                <p>♿ Personas con Discapacidad</p>
            </div>
            <div class="card">
                <h3><?php echo $diagnosticos; ?></h3>
                <p>💊 Diagnósticos Registrados</p>
            </div>
            <div class="card">
                <h3><?php echo $logs_hoy; ?></h3>
                <p>📋 Logs registrados hoy</p>
            </div>
        </div>
    </div>
</body>
</html>
