<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header("Location: index.php");
    exit;
}

require 'backend/config.php';
require 'backend/reservas/Reservas.php';
require 'backend/usuarios/Usuario.php';

$db = (new Database())->getConnection();
$reservas = new Reservas($db);
$usuario = new Usuario($db);

$usuarioActual = $usuario->obtenerUsuarioPorId($_SESSION['usuario_id']);
if ($usuarioActual['rol'] !== 'profesional') {
    header("Location: acceso_denegado.php");
    exit;
}

// Obtener reservas asignadas al profesional
$query = $db->prepare("SELECT r.id, u.nombre AS paciente, s.nombre AS servicio, r.fecha, r.hora, r.estado
                       FROM reservas r
                       JOIN usuarios u ON r.usuario_id = u.id
                       JOIN servicios s ON r.servicio_id = s.id
                       WHERE r.profesional_id = :profesional_id
                       ORDER BY r.fecha ASC, r.hora ASC");
$query->bindParam(':profesional_id', $usuarioActual['id']);
$query->execute();
$lista_reservas = $query->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agenda</title>
    <link rel="stylesheet" href="/dentista-pro/assets/css/styles.css">
</head>
<body>
    <div class="container">
        <h1>Agenda</h1>
        <a href="dashboard.php" class="btn">Volver al Dashboard</a>

        <?php if (count($lista_reservas) > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>Paciente</th>
                        <th>Servicio</th>
                        <th>Fecha</th>
                        <th>Hora</th>
                        <th>Estado</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($lista_reservas as $reserva): ?>
                        <tr>
                            <td><?= htmlspecialchars($reserva['paciente']); ?></td>
                            <td><?= htmlspecialchars($reserva['servicio']); ?></td>
                            <td><?= htmlspecialchars($reserva['fecha']); ?></td>
                            <td><?= htmlspecialchars($reserva['hora']); ?></td>
                            <td><?= htmlspecialchars($reserva['estado']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No tienes citas programadas.</p>
        <?php endif; ?>
    </div>
</body>
</html>
