<?php
class Reservas {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Crear una nueva reserva
    public function crearReserva($usuario_id, $servicio_id, $fecha, $hora, $profesional_id = null) {
        $query = "INSERT INTO reservas (usuario_id, servicio_id, fecha, hora, estado, profesional_id) 
                  VALUES (:usuario_id, :servicio_id, :fecha, :hora, 'confirmada', :profesional_id)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':usuario_id', $usuario_id);
        $stmt->bindParam(':servicio_id', $servicio_id);
        $stmt->bindParam(':fecha', $fecha);
        $stmt->bindParam(':hora', $hora);
        $stmt->bindParam(':profesional_id', $profesional_id);
        return $stmt->execute();
    }

    // Listar reservas por usuario (paciente)
    public function listarReservas($usuario_id) {
        $query = "SELECT r.id, s.nombre AS servicio, r.fecha, r.hora, r.estado, 
                         COALESCE(u.nombre, 'No asignado') AS profesional
                  FROM reservas r
                  JOIN servicios s ON r.servicio_id = s.id
                  LEFT JOIN usuarios u ON r.profesional_id = u.id
                  WHERE r.usuario_id = :usuario_id
                  ORDER BY r.fecha ASC, r.hora ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':usuario_id', $usuario_id);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Listar reservas asignadas a un profesional
    public function listarReservasPorProfesional($profesional_id) {
        $query = "SELECT r.id, u.nombre AS paciente, s.nombre AS servicio, r.fecha, r.hora, r.estado
                  FROM reservas r
                  JOIN usuarios u ON r.usuario_id = u.id
                  JOIN servicios s ON r.servicio_id = s.id
                  WHERE r.profesional_id = :profesional_id
                  ORDER BY r.fecha ASC, r.hora ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':profesional_id', $profesional_id);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Actualizar el estado de una reserva
    public function actualizarEstadoReserva($reserva_id, $nuevo_estado) {
        $query = "UPDATE reservas SET estado = :estado WHERE id = :reserva_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':estado', $nuevo_estado);
        $stmt->bindParam(':reserva_id', $reserva_id);
        return $stmt->execute();
    }

    // Cancelar (eliminar) una reserva
    public function cancelarReserva($reserva_id) {
        $query = "DELETE FROM reservas WHERE id = :reserva_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':reserva_id', $reserva_id);
        return $stmt->execute();
    }
}
?>
