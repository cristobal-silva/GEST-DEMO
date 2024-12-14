<?php
require __DIR__ . '/backend/config.php'; // Ajustamos la ruta relativa
$db = (new Database())->getConnection();

$mensaje = null; // Variable para mensajes de error o éxito

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = isset($_POST['nombre']) ? trim($_POST['nombre']) : null;
    $correo = isset($_POST['correo']) ? trim($_POST['correo']) : null;
    $contraseña = isset($_POST['contraseña']) ? trim($_POST['contraseña']) : null;

    if (empty($nombre) || empty($correo) || empty($contraseña)) {
        $mensaje = "Todos los campos son obligatorios.";
    } else {
        try {
            $query_check = $db->prepare("SELECT id FROM usuarios WHERE correo = :correo");
            $query_check->bindParam(':correo', $correo);
            $query_check->execute();

            if ($query_check->rowCount() > 0) {
                $mensaje = "El correo ya está registrado. Por favor, usa otro.";
            } else {
                $query = $db->prepare("
                    INSERT INTO usuarios (nombre, correo, contraseña, rol) 
                    VALUES (:nombre, :correo, :contraseña, 'paciente')
                ");
                $query->bindParam(':nombre', $nombre);
                $query->bindParam(':correo', $correo);
                $query->bindParam(':contraseña', $contraseña);

                if ($query->execute()) {
                    header("Location: index.php?registro=exitoso");
                    exit;
                } else {
                    $mensaje = "Error al registrar usuario. Por favor, intenta de nuevo.";
                }
            }
        } catch (PDOException $e) {
            $mensaje = "Error de base de datos: " . $e->getMessage();
        }
    }
}
?>
