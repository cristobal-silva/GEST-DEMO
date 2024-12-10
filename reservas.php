<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header("Location: index.php");
    exit;
}

require 'backend/config.php';
require 'backend/reservas/Reservas.php';
require 'backend/servicios/Servicios.php';
require 'backend/usuarios/Usuario.php';

$db = (new Database())->getConnection();
$reservas = new Reservas($db);
$servicios = new Servicios($db);
$usuario = new Usuario($db);

$usuario_id = $_SESSION['usuario_id'];

// Obtener la lista de servicios
$lista_servicios = $servicios->listarServicios();

// Obtener la lista de profesionales
$query = $db->prepare("SELECT id, nombre FROM usuarios WHERE rol = 'profesional'");
$query->execute();
$lista_profesionales = $query->fetchAll(PDO::FETCH_ASSOC);

// Crear una nueva reserva
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['crear_reserva'])) {
    $servicio_id = $_POST['servicio_id'];
    $fecha = $_POST['fecha'];
    $hora = $_POST['hora'];
    $profesional_id = $_POST['profesional_id'];
    $reservas->crearReserva($usuario_id, $servicio_id, $fecha, $hora, $profesional_id);
}

// Eliminar una reserva
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['eliminar_reserva'])) {
    $reserva_id = $_POST['reserva_id'];
    $reservas->cancelarReserva($reserva_id);
}

// Listar reservas del paciente
$lista_reservas = $reservas->listarReservas($usuario_id);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Reservas</title>
    <link rel="stylesheet" href="/dentista-pro/assets/css/styles.css">
</head>
<body>
    <div class="container">
        <h1>Gestión de Reservas</h1>
        <a href="dashboard.php" class="btn">Volver al Dashboard</a>

        <h2>Crear Nueva Reserva</h2>
        <form method="POST">
            <input type="hidden" name="crear_reserva" value="1">
            <div class="form-group">
                <label for="servicio_id">Servicio</label>
                <select id="servicio_id" name="servicio_id" required>
                    <option value="">Selecciona un servicio</option>
                    <?php foreach ($lista_servicios as $servicio): ?>
                        <option value="<?= $servicio['id']; ?>"><?= htmlspecialchars($servicio['nombre']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="profesional_id">Profesional</label>
                <select id="profesional_id" name="profesional_id" required>
                    <option value="">Selecciona un profesional</option>
                    <?php foreach ($lista_profesionales as $profesional): ?>
                        <option value="<?= $profesional['id']; ?>"><?= htmlspecialchars($profesional['nombre']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="fecha">Fecha</label>
                <input type="date" id="fecha" name="fecha" required>
            </div>
            <div class="form-group">
                <label for="hora">Hora</label>
                <input type="time" id="hora" name="hora" required>
            </div>
            <button type="submit" class="btn">Crear Reserva</button>
        </form>

        <h2>Tus Reservas</h2>
        <?php if (count($lista_reservas) > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>Servicio</th>
                        <th>Fecha</th>
                        <th>Hora</th>
                        <th>Profesional</th>
                        <th>Estado</th>
                        <th>Acción</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($lista_reservas as $reserva): ?>
                        <tr>
                            <td><?= htmlspecialchars($reserva['servicio']); ?></td>
                            <td><?= htmlspecialchars($reserva['fecha']); ?></td>
                            <td><?= htmlspecialchars($reserva['hora']); ?></td>
                            <td><?= htmlspecialchars($reserva['profesional']); ?></td>
                            <td><?= htmlspecialchars($reserva['estado']); ?></td>
                            <td>
                                <form method="POST" style="display: inline;">
                                    <input type="hidden" name="eliminar_reserva" value="1">
                                    <input type="hidden" name="reserva_id" value="<?= $reserva['id']; ?>">
                                    <button type="submit" class="btn btn-logout">Eliminar</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No tienes reservas activas.</p>
        <?php endif; ?>
    </div>
</body>
</html>
