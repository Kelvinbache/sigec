<?php
class LogManager
{
    private $pdo;
    private $ip_address;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
        $this->ip_address = $this->getClientIP();
    }

    private function getClientIP()
    {
        if (isset($_SERVER["HTTP_CLIENT_IP"])) {
            return $_SERVER["HTTP_CLIENT_IP"];
        }
        if (isset($_SERVER["HTTP_X_FORWARDED_FOR"])) {
            return $_SERVER["HTTP_X_FORWARDED_FOR"];
        }
        return isset($_SERVER["REMOTE_ADDR"])
            ? $_SERVER["REMOTE_ADDR"]
            : "UNKNOWN";
    }

    public function registrar($usuario_id, $accion, $detalle = "")
    {
        try {
            $stmt = $this->pdo
                ->prepare("INSERT INTO logs (usuario_id, accion, detalle, ip_address, fecha)
                                         VALUES (:usuario_id, :accion, :detalle, :ip, NOW())");
            return $stmt->execute([
                ":usuario_id" => $usuario_id,
                ":accion" => $accion,
                ":detalle" => $detalle,
                ":ip" => $this->ip_address,
            ]);
        } catch (PDOException $e) {
            return false;
        }
    }
}
?>
