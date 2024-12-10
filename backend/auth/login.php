<?php
session_start();
require '../config.php';

$db = (new Database())->getConnection();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $correo = $_POST['correo'];
    $contraseña = $_POST['contraseña'];

    $query = $db->prepare("SELECT * FROM usuarios WHERE correo = :correo");
    $query->bindParam(':correo', $correo);
    $query->execute();

    $usuario = $query->fetch(PDO::FETCH_ASSOC);

    if ($usuario && password_verify($contraseña, $usuario['contraseña'])) {
        $_SESSION['usuario_id'] = $usuario['id'];
        $_SESSION['rol'] = $usuario['rol'];
        header("Location: ../../dashboard.php");
    } else {
        echo "Correo o contraseña incorrectos.";
    }
}
?>
