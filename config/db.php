<?php

define("DB_HOST", "localhost"); // Servidor de la base de datos
define("DB_USER", "root"); // Usuario de la base de datos
define("DB_PASS", ""); // Contraseña (por defecto vacío en XAMPP/WAMP)
define("DB_NAME", "sigec"); // Nombre de la base de datos
define("DB_CHARSET", "utf8mb4"); // Juego de caracteres

$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if ($conn->connect_error) {
    die("Error de conexión a la base de datos: " . $conn->connect_error);
}

$conn->set_charset(DB_CHARSET);

date_default_timezone_set("America/Santo_Domingo");

?>
