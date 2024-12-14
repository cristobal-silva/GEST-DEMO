<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header("Location: index.php");
    exit;
}

require 'backend/config.php';
require 'backend/reservas/Reservas.php';

$db = (new Database())->getConnection();
$reservas = new Reservas($db);

// Obtener las reservas del profesional actual
$profesional_id = $_SESSION['usuario_id'];
$lista_reservas = $reservas->listarReservasPorProfesional($profesional_id);

// Si se envió una solicitud POST para actualizar el estado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $reserva_id = $_POST['reserva_id'];
    $nuevo_estado = $_POST['estado'];
    $reservas->actualizarEstadoReserva($reserva_id, $nuevo_estado);
    header("Location: agenda.php");
    exit;
}
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
        <table>
            <thead>
                <tr>
                    <th>Paciente</th>
                    <th>Servicio</th>
                    <th>Fecha</th>
                    <th>Hora</th>
                    <th>Estado</th>
                    <th>Acciones</th>
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
                        <td>
                            <form method="POST" style="display: inline;">
                                <input type="hidden" name="reserva_id" value="<?= $reserva['id']; ?>">
                                <input type="hidden" name="estado" value="confirmada">
                                <button type="submit" class="btn">Confirmar</button>
                            </form>
                            <form method="POST" style="display: inline;">
                                <input type="hidden" name="reserva_id" value="<?= $reserva['id']; ?>">
                                <input type="hidden" name="estado" value="completada">
<button class="btn-completar" onclick="completarCita(<?= $reserva['id']; ?>)">Completar</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
