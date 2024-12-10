<?php
class Usuario {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Listar todos los usuarios
    public function listarUsuarios() {
        $query = "SELECT id, nombre, correo, contrasena, rol, creado_en FROM usuarios ORDER BY id ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Obtener información de un usuario por su ID
    public function obtenerUsuarioPorId($id) {
        $query = "SELECT id, nombre, correo, rol FROM usuarios WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Crear un nuevo usuario
    public function crearUsuario($nombre, $correo, $contrasena, $rol) {
        $query = "INSERT INTO usuarios (nombre, correo, contrasena, rol, creado_en) 
                  VALUES (:nombre, :correo, :contrasena, :rol, NOW())";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':nombre', $nombre);
        $stmt->bindParam(':correo', $correo);
        $stmt->bindParam(':contrasena', $contrasena);
        $stmt->bindParam(':rol', $rol);
        return $stmt->execute();
    }

    // Editar un usuario existente
    public function editarUsuario($id, $nombre, $correo, $rol) {
        $query = "UPDATE usuarios SET nombre = :nombre, correo = :correo, rol = :rol WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':nombre', $nombre);
        $stmt->bindParam(':correo', $correo);
        $stmt->bindParam(':rol', $rol);
        return $stmt->execute();
    }

    // Eliminar un usuario
    public function eliminarUsuario($id) {
        $query = "DELETE FROM usuarios WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }
}
?>
