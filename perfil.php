<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header("Location: index.php");
    exit;
}

require 'backend/config.php';

$db = (new Database())->getConnection();

// Obtener los datos del usuario
$usuario_id = $_SESSION['usuario_id'];
$query = $db->prepare("SELECT nombre, correo FROM usuarios WHERE id = :usuario_id");
$query->bindParam(':usuario_id', $usuario_id);
$query->execute();
$usuario = $query->fetch(PDO::FETCH_ASSOC);

// Si se envía un formulario para actualizar el perfil
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nuevo_nombre = $_POST['nombre'];
    $nuevo_correo = $_POST['correo'];

    $update_query = $db->prepare("UPDATE usuarios SET nombre = :nombre, correo = :correo WHERE id = :usuario_id");
    $update_query->bindParam(':nombre', $nuevo_nombre);
    $update_query->bindParam(':correo', $nuevo_correo);
    $update_query->bindParam(':usuario_id', $usuario_id);

    if ($update_query->execute()) {
        $mensaje = "Perfil actualizado exitosamente.";
        $usuario['nombre'] = $nuevo_nombre;
        $usuario['correo'] = $nuevo_correo;
    } else {
        $mensaje = "Hubo un error al actualizar el perfil.";
    }
}

// Obtener el historial de reservas del usuario
$reservas_query = $db->prepare("
    SELECT s.nombre AS servicio, r.fecha, r.hora, r.estado, 
           COALESCE(u.nombre, 'No asignado') AS profesional 
    FROM reservas r
    JOIN servicios s ON r.servicio_id = s.id
    LEFT JOIN usuarios u ON r.profesional_id = u.id
    WHERE r.usuario_id = :usuario_id
    ORDER BY r.fecha DESC, r.hora DESC
");
$reservas_query->bindParam(':usuario_id', $usuario_id);
$reservas_query->execute();
$historial_reservas = $reservas_query->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi Perfil</title>
    <link rel="stylesheet" href="/dentista-pro/assets/css/styles.css">
</head>
<body>
    <div class="container">
        <h1>Mi Perfil</h1>
        <?php if (isset($mensaje)): ?>
            <div class="alert"><?= htmlspecialchars($mensaje); ?></div>
        <?php endif; ?>
        <form method="POST">
            <div class="form-group">
                <label for="nombre">Nombre</label>
                <input type="text" id="nombre" name="nombre" value="<?= htmlspecialchars($usuario['nombre']); ?>" required>
            </div>
            <div class="form-group">
                <label for="correo">Correo Electrónico</label>
                <input type="email" id="correo" name="correo" value="<?= htmlspecialchars($usuario['correo']); ?>" required>
            </div>
            <button type="submit" class="btn">Actualizar Perfil</button>
        </form>

        <h2>Historial de Actividades</h2>
        <?php if (count($historial_reservas) > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>Servicio</th>
                        <th>Fecha</th>
                        <th>Hora</th>
                        <th>Estado</th>
                        <th>Profesional</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($historial_reservas as $reserva): ?>
                        <tr>
                            <td><?= htmlspecialchars($reserva['servicio']); ?></td>
                            <td><?= htmlspecialchars($reserva['fecha']); ?></td>
                            <td><?= htmlspecialchars($reserva['hora']); ?></td>
                            <td><?= htmlspecialchars($reserva['estado']); ?></td>
                            <td><?= htmlspecialchars($reserva['profesional']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No tienes actividades registradas.</p>
        <?php endif; ?>

        <a href="dashboard.php" class="btn">Volver al Dashboard</a>
    </div>
</body>
</html>
