<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header("Location: index.php");
    exit;
}

require 'backend/config.php';
require 'backend/reservas/reservas.php';

$db = (new Database())->getConnection();
$reservas = new Reservas($db);

// Obtener los datos del usuario
$usuario_id = $_SESSION['usuario_id'];
$query = $db->prepare("SELECT nombre, correo FROM usuarios WHERE id = :usuario_id");
$query->bindParam(':usuario_id', $usuario_id);
$query->execute();
$usuario = $query->fetch(PDO::FETCH_ASSOC);

// Mensaje de confirmación o error
$mensaje = null;

// Si el paciente anula una reserva
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['anular_reserva_id'])) {
    $reserva_id = $_POST['anular_reserva_id'];
    if ($reservas->anularReserva($reserva_id)) {
        $mensaje = "La reserva ha sido anulada correctamente.";
    } else {
        $mensaje = "Error al anular la reserva. Verifica el estado.";
    }
}

// Si el paciente actualiza su perfil
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['nombre']) && isset($_POST['correo'])) {
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
    SELECT r.id, s.nombre AS servicio, r.fecha, r.hora, r.estado, 
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

        <!-- Mensaje de confirmación o error -->
        <?php if ($mensaje): ?>
            <div class="alert"><?= htmlspecialchars($mensaje); ?></div>
        <?php endif; ?>

        <!-- Formulario de actualización de perfil -->
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

        <!-- Historial de Actividades -->
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
                        <th>Acciones</th>
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
                            <td>
                                <!-- Botón para anular reserva -->
                                <?php if ($reserva['estado'] !== 'completada' && $reserva['estado'] !== 'anulada'): ?>
                                    <form method="POST" style="display: inline-block;">
                                        <input type="hidden" name="anular_reserva_id" value="<?= $reserva['id']; ?>">
                                        <button type="submit" class="btn-danger">Anular</button>
                                    </form>
                                <?php else: ?>
                                    <span class="disabled">No disponible</span>
                                <?php endif; ?>
                            </td>
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
