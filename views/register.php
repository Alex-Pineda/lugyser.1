<?php
require_once '../config/database.php';
require_once '../models/UsuarioModel.php';
require_once '../controllers/AuthController.php';

$db = new Database();
$usuarioModel = new UsuarioModel($db->getConnection());
$authController = new AuthController($usuarioModel, null);

$mensaje = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $datos = [
        'nombre' => $_POST['nombre'],
        'apellido' => $_POST['apellido'],
        'nombre_usuario' => $_POST['nombre_usuario'],
        'contrasena' => $_POST['contrasena'],
        'tipo_documento' => $_POST['tipo_documento'],
        'documento_identidad' => $_POST['documento_identidad'],
        'email' => $_POST['email'],
        'telefono' => $_POST['telefono']
    ];
    if ($authController->register($datos)) {
        $mensaje = '<div class="alert alert-success">Registro exitoso. Ahora puedes iniciar sesión.</div>';
    } else {
        $mensaje = '<div class="alert alert-danger">Error al registrar el usuario. Por favor, inténtelo de nuevo.</div>';
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Usuario - Lugyser</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h1 class="text-center">Registro de Usuario</h1>
        <?php echo $mensaje; ?>
        <form method="POST" action="register.php" class="mt-4">
            <div class="form-group">
                <label for="nombre">Nombre</label>
                <input type="text" name="nombre" id="nombre" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="apellido">Apellido</label>
                <input type="text" name="apellido" id="apellido" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="nombre_usuario">Nombre de Usuario</label>
                <input type="text" name="nombre_usuario" id="nombre_usuario" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="contrasena">Contraseña</label>
                <input type="password" name="contrasena" id="contrasena" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="tipo_documento">Tipo de Documento</label>
                <input type="text" name="tipo_documento" id="tipo_documento" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="documento_identidad">Documento de Identidad</label>
                <input type="text" name="documento_identidad" id="documento_identidad" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="email">Correo Electrónico</label>
                <input type="email" name="email" id="email" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="telefono">Teléfono</label>
                <input type="text" name="telefono" id="telefono" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary btn-block">Registrarse</button>
        </form>
    </div>
</body>
</html>
