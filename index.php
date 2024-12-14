<?php
session_start();
if (isset($_SESSION['usuario_id'])) {
    header("Location: dashboard.php");
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require 'backend/config.php';
    $db = (new Database())->getConnection();

    $correo = $_POST['correo'];
    $contrasena = $_POST['contrasena'];

    // Buscar usuario por correo
    $query = $db->prepare("SELECT * FROM usuarios WHERE correo = :correo");
    $query->bindParam(':correo', $correo);
    $query->execute();

    $usuario = $query->fetch(PDO::FETCH_ASSOC);

    // Comparar contraseñas en texto plano
    if ($usuario && $usuario['contrasena'] === $contrasena) {
        $_SESSION['usuario_id'] = $usuario['id'];
        $_SESSION['rol'] = $usuario['rol'];
        header("Location: dashboard.php");
        exit;
    } else {
        $error = "Correo o contrasena incorrectos.";
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión</title>
    <link rel="stylesheet" href="/dentista-pro/assets/css/styles.css">
</head>
<body>
    <div class="container">
        <h1>Iniciar Sesión</h1>
        <?php if ($error): ?>
            <div class="alert"><?= $error; ?></div>
        <?php endif; ?>
        <form method="POST">
            <div class="form-group">
                <label for="correo">Correo Electrónico</label>
                <input type="email" name="correo" id="correo" required>
            </div>
            <div class="form-group">
                <label for="contrasena">contraseña</label>
                <input type="password" name="contrasena" id="contrasena" required>
            </div>
            <button type="submit" class="btn">Ingresar</button>
        </form>
        <p>¿No tienes una cuenta? <a href="register.php">Regístrate aquí</a></p>
    </div>
</body>
</html>
