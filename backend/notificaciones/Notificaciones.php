<?php
class Notificacion {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Crear una nueva notificación
    public function crearNotificacion($usuario_id, $mensaje) {
        $query = "INSERT INTO notificaciones (usuario_id, mensaje) VALUES (:usuario_id, :mensaje)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':usuario_id', $usuario_id);
        $stmt->bindParam(':mensaje', $mensaje);
        return $stmt->execute();
    }

    // Obtener todas las notificaciones de un usuario
    public function obtenerNotificaciones($usuario_id) {
        $query = "SELECT * FROM notificaciones WHERE usuario_id = :usuario_id ORDER BY fecha DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':usuario_id', $usuario_id);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Marcar una notificación como leída
    public function marcarComoLeida($notificacion_id) {
        $query = "UPDATE notificaciones SET leido = 1 WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $notificacion_id);
        return $stmt->execute();
    }

    // Marcar todas las notificaciones como leídas
    public function marcarTodasComoLeidas($usuario_id) {
        $query = "UPDATE notificaciones SET leido = 1 WHERE usuario_id = :usuario_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':usuario_id', $usuario_id);
        return $stmt->execute();
    }
}
?>
