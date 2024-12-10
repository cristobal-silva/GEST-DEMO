<?php
class Servicios {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Listar todos los servicios
    public function listarServicios() {
        $query = "SELECT * FROM servicios";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Crear un nuevo servicio
    public function crearServicio($nombre, $descripcion, $duracion, $precio) {
        $query = "INSERT INTO servicios (nombre, descripcion, duracion, precio) 
                  VALUES (:nombre, :descripcion, :duracion, :precio)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':nombre', $nombre);
        $stmt->bindParam(':descripcion', $descripcion);
        $stmt->bindParam(':duracion', $duracion);
        $stmt->bindParam(':precio', $precio);
        return $stmt->execute();
    }

    // Editar un servicio existente
    public function editarServicio($id, $nombre, $descripcion, $duracion, $precio) {
        $query = "UPDATE servicios 
                  SET nombre = :nombre, descripcion = :descripcion, duracion = :duracion, precio = :precio 
                  WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':nombre', $nombre);
        $stmt->bindParam(':descripcion', $descripcion);
        $stmt->bindParam(':duracion', $duracion);
        $stmt->bindParam(':precio', $precio);
        return $stmt->execute();
    }

    // Eliminar un servicio
    public function eliminarServicio($id) {
        $query = "DELETE FROM servicios WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }
}
?>
