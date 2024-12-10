<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header("Location: index.php");
    exit;
}

require 'backend/config.php';
require 'backend/servicios/Servicios.php';
require 'backend/usuarios/Usuario.php';

$db = (new Database())->getConnection();
$servicios = new Servicios($db);
$usuario = new Usuario($db);

// Verificar si el usuario es administrador o profesional
$usuarioActual = $usuario->obtenerUsuarioPorId($_SESSION['usuario_id']);
if (!in_array($usuarioActual['rol'], ['administrador', 'profesional'])) {
    header("Location: acceso_denegado.php");
    exit;
}

// Añadir servicio
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['crear_servicio'])) {
    $nombre = $_POST['nombre'];
    $descripcion = $_POST['descripcion'];
    $duracion = $_POST['duracion'];
    $precio = $_POST['precio'];
    $servicios->crearServicio($nombre, $descripcion, $duracion, $precio);
}

// Editar servicio
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['editar_servicio'])) {
    $id = $_POST['id'];
    $nombre = $_POST['nombre'];
    $descripcion = $_POST['descripcion'];
    $duracion = $_POST['duracion'];
    $precio = $_POST['precio'];
    $servicios->editarServicio($id, $nombre, $descripcion, $duracion, $precio);
}

// Eliminar servicio
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['eliminar_servicio'])) {
    $id = $_POST['id'];
    $servicios->eliminarServicio($id);
}

// Listar servicios
$lista_servicios = $servicios->listarServicios();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Servicios</title>
    <link rel="stylesheet" href="/dentista-pro/assets/css/styles.css">
</head>
<body>
    <div class="container">
        <h1>Gestión de Servicios</h1>
        <a href="dashboard.php" class="btn">Volver al Dashboard</a>

        <h2>Añadir Nuevo Servicio</h2>
        <form method="POST">
            <input type="hidden" name="crear_servicio" value="1">
            <div class="form-group">
                <label for="nombre">Nombre</label>
                <input type="text" id="nombre" name="nombre" required>
            </div>
            <div class="form-group">
                <label for="descripcion">Descripción</label>
                <textarea id="descripcion" name="descripcion" required></textarea>
            </div>
            <div class="form-group">
                <label for="duracion">Duración (en minutos)</label>
                <input type="number" id="duracion" name="duracion" required>
            </div>
            <div class="form-group">
                <label for="precio">Precio</label>
                <input type="number" step="0.01" id="precio" name="precio" required>
            </div>
            <button type="submit" class="btn">Añadir Servicio</button>
        </form>

        <h2>Lista de Servicios</h2>
        <table>
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Descripción</th>
                    <th>Duración</th>
                    <th>Precio</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($lista_servicios as $servicio): ?>
                    <tr>
                        <form method="POST">
                            <td><input type="text" name="nombre" value="<?= htmlspecialchars($servicio['nombre']); ?>" required></td>
                            <td><textarea name="descripcion" required><?= htmlspecialchars($servicio['descripcion']); ?></textarea></td>
                            <td><input type="number" name="duracion" value="<?= htmlspecialchars($servicio['duracion']); ?>" required></td>
                            <td><input type="number" step="0.01" name="precio" value="<?= htmlspecialchars($servicio['precio']); ?>" required></td>
                            <td>
                                <input type="hidden" name="id" value="<?= $servicio['id']; ?>">
                                <button type="submit" name="editar_servicio" class="btn">Guardar</button>
                                <button type="submit" name="eliminar_servicio" class="btn btn-logout">Eliminar</button>
                            </td>
                        </form>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
