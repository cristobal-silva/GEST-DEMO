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

// Verificar que el usuario actual sea administrador
$query = $db->prepare("SELECT rol FROM usuarios WHERE id = :id");
$query->bindParam(':id', $_SESSION['usuario_id']);
$query->execute();
$rol_actual = $query->fetchColumn();

if ($rol_actual !== 'administrador') {
    header("Location: acceso_denegado.php");
    exit;
}

// Crear usuario
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['crear_usuario'])) {
    $nombre = $_POST['nombre'];
    $correo = $_POST['correo'];
    $contrasena = $_POST['contrasena'];
    $rol = $_POST['rol'];
    $usuario->crearUsuario($nombre, $correo, $contrasena, $rol);
}

// Editar usuario
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['editar_usuario'])) {
    $id = $_POST['id'];
    $nombre = $_POST['nombre'];
    $correo = $_POST['correo'];
    $rol = $_POST['rol'];
    $usuario->editarUsuario($id, $nombre, $correo, $rol);
}

// Eliminar usuario
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['eliminar_usuario'])) {
    $id = $_POST['id'];
    $usuario->eliminarUsuario($id);
}

// Listar usuarios
$usuarios = $usuario->listarUsuarios();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Usuarios</title>
    <link rel="stylesheet" href="/dentista-pro/assets/css/styles.css">
</head>
<body>
    <div class="container">
        <h1>Gestión de Usuarios</h1>
        <a href="dashboard.php" class="btn">Volver al Dashboard</a>

        <h2>Crear Nuevo Usuario</h2>
        <form method="POST">
            <input type="hidden" name="crear_usuario" value="1">
            <div class="form-group">
                <label for="nombre">Nombre</label>
                <input type="text" id="nombre" name="nombre" required>
            </div>
            <div class="form-group">
                <label for="correo">Correo</label>
                <input type="email" id="correo" name="correo" required>
            </div>
            <div class="form-group">
                <label for="contrasena">Contrasena</label>
                <input type="password" id="contrasena" name="contrasena" required>
            </div>
            <div class="form-group">
                <label for="rol">Rol</label>
                <select id="rol" name="rol" required>
                    <option value="administrador">Administrador</option>
                    <option value="paciente">Paciente</option>
                    <option value="profesional">Profesional</option>
                </select>
            </div>
            <button type="submit" class="btn">Crear Usuario</button>
        </form>

        <h2>Lista de Usuarios</h2>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Correo</th>
                    <th>Rol</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($usuarios as $u): ?>
                    <tr>
                        <form method="POST">
                            <td><?= htmlspecialchars($u['id']); ?></td>
                            <td><input type="text" name="nombre" value="<?= htmlspecialchars($u['nombre']); ?>" required></td>
                            <td><input type="email" name="correo" value="<?= htmlspecialchars($u['correo']); ?>" required></td>
                            <td>
                                <select name="rol" required>
                                    <option value="administrador" <?= $u['rol'] === 'administrador' ? 'selected' : ''; ?>>Administrador</option>
                                    <option value="paciente" <?= $u['rol'] === 'paciente' ? 'selected' : ''; ?>>Paciente</option>
                                    <option value="profesional" <?= $u['rol'] === 'profesional' ? 'selected' : ''; ?>>Profesional</option>
                                </select>
                            </td>
                            <td>
                                <input type="hidden" name="id" value="<?= $u['id']; ?>">
                                <button type="submit" name="editar_usuario" class="btn">Guardar</button>
                                <button type="submit" name="eliminar_usuario" class="btn btn-logout">Eliminar</button>
                            </td>
                        </form>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
