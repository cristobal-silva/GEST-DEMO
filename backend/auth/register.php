<?php
require '../config.php';
$db = (new Database())->getConnection();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = $_POST['nombre'];
    $correo = $_POST['correo'];
    $contraseña = $_POST['contraseña']; // Contraseña en texto plano

    $query = $db->prepare("INSERT INTO usuarios (nombre, correo, contraseña, rol) VALUES (:nombre, :correo, :contraseña, 'paciente')");
    $query->bindParam(':nombre', $nombre);
    $query->bindParam(':correo', $correo);
    $query->bindParam(':contraseña', $contraseña);

    if ($query->execute()) {
        echo "Usuario registrado con éxito.";
        header("Location: ../../index.php");
    } else {
        echo "Error al registrar usuario.";
    }
}
?>
