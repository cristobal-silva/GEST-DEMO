<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header("Location: index.php");
    exit;
}

require 'backend/config.php';
require 'backend/usuarios/Usuario.php';

$db = (new Database())->getConnection();
$usuario = new Usuario($db);

// Obtener información del usuario actual
$usuario_actual = $usuario->obtenerUsuarioPorId($_SESSION['usuario_id']);
if (!$usuario_actual) {
    echo "Error: Usuario no encontrado.";
    exit;
}

// Simular datos para mostrar métricas (esto debería venir de la BD)
$metricas = [
    'total_reservas' => 25,
    'usuarios_activos' => 50,
    'servicios_ofrecidos' => 10,
];
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="/dentista-pro/assets/css/styles.css">
</head>
<body class="dashboard-page">
    <!-- Barra de Navegación -->
    <nav class="navbar">
        <div class="logo">Gestión Dental</div>
        <ul class="nav-links">
            <?php if ($usuario_actual['rol'] === 'administrador'): ?>
                <li><a href="usuarios.php">Gestión de Usuarios</a></li>
                <li><a href="servicios.php">Gestión de Servicios</a></li>
            <?php endif; ?>

            <?php if ($usuario_actual['rol'] === 'paciente'): ?>
                <li><a href="reservas.php">Gestión de Reservas</a></li>
                <li><a href="perfil.php">Mi Perfil</a></li>
            <?php endif; ?>

            <?php if ($usuario_actual['rol'] === 'profesional'): ?>
                <li><a href="agenda.php">Ver Agenda</a></li>
            <?php endif; ?>

            <li><a href="logout.php" class="btn-danger">Cerrar Sesión</a></li>
        </ul>
    </nav>

    <!-- Contenido Principal -->
    <main class="dashboard-container">
        <h1>Bienvenido, <?= htmlspecialchars($usuario_actual['nombre']); ?></h1>
        <p>Rol: <?= htmlspecialchars($usuario_actual['rol']); ?></p>

        <!-- Métricas -->
		<?php if ($usuario_actual['rol'] === 'administrador'): ?>

        <section class="metrics-section">
            <div class="metric-card">
                <h3><?= $metricas['total_reservas']; ?></h3>
                <p>Total de Reservas</p>
            </div>
            <div class="metric-card">
                <h3><?= $metricas['usuarios_activos']; ?></h3>
                <p>Usuarios Activos</p>
            </div>
            <div class="metric-card">
                <h3><?= $metricas['servicios_ofrecidos']; ?></h3>
                <p>Servicios Ofrecidos</p>
            </div>
        </section>
		<?php endif; ?>
    </main>
</body>
</html>
