<?php
require __DIR__ . '/backend/config.php'; // Ajustamos la ruta relativa
$db = (new Database())->getConnection();

$mensaje = null; // Variable para mensajes de error o éxito

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Capturar los datos del formulario
    $nombre = isset($_POST['nombre']) ? trim($_POST['nombre']) : null;
    $correo = isset($_POST['correo']) ? trim($_POST['correo']) : null;
    $contrasena = isset($_POST['contrasena']) ? trim($_POST['contrasena']) : null;

    // Validar que los campos no estén vacíos
    if (empty($nombre) || empty($correo) || empty($contrasena)) {
        $mensaje = "Todos los campos son obligatorios.";
    } else {
        try {
            // Verificar si el correo ya está registrado
            $query_check = $db->prepare("SELECT id FROM usuarios WHERE correo = :correo");
            $query_check->bindParam(':correo', $correo, PDO::PARAM_STR);
            $query_check->execute();

            if ($query_check->rowCount() > 0) {
                $mensaje = "El correo ya está registrado. Por favor, usa otro.";
            } else {
                // Insertar el usuario
                $query = $db->prepare("
                    INSERT INTO usuarios (nombre, correo, contrasena, rol) 
                    VALUES (:nombre, :correo, :contrasena, 'paciente')
                ");
                $query->bindParam(':nombre', $nombre, PDO::PARAM_STR);
                $query->bindParam(':correo', $correo, PDO::PARAM_STR);
                $query->bindParam(':contrasena', $contrasena, PDO::PARAM_STR);

                if ($query->execute()) {
header("Location: /dentista-pro/index.php?registro=exitoso");
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

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro</title>
    <link rel="stylesheet" href="/dentista-pro/assets/css/styles.css">
</head>
<body>
    <div class="container">
        <h1>Registro</h1>
        <?php if (!empty($mensaje)): ?>
            <div class="alert"><?= htmlspecialchars($mensaje); ?></div>
        <?php endif; ?>
        <form method="POST" action="">
            <div class="form-group">
                <label for="nombre">Nombre</label>
                <input type="text" id="nombre" name="nombre" placeholder="Ingresa tu nombre" required>
            </div>
            <div class="form-group">
                <label for="correo">Correo Electrónico</label>
                <input type="email" id="correo" name="correo" placeholder="Ingresa tu correo" required>
            </div>
            <div class="form-group">
                <label for="contrasena">Contraseña</label>
                <input type="password" id="contrasena" name="contrasena" placeholder="Ingresa tu contrasena" required>
            </div>
            <button type="submit" class="btn">Registrar</button>
        </form>
        <a href="/dentista-pro/index.php" class="btn">Volver al Inicio</a>
    </div>
</body>
</html>
