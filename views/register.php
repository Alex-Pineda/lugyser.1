<?php
session_start();
if (isset($_SESSION['usuario'])) {
    header("Location: ../index.php"); // Redirigir al nuevo index.php si ya hay sesión
    exit;
}

require_once '../config/database.php';
require_once '../models/UsuarioModel.php';
require_once '../controllers/AuthController.php';

$db = new Database();
$usuarioModel = new UsuarioModel($db->getConnection());
$authController = new AuthController($usuarioModel, null);

$mensaje = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $datos = [
        'nombre' => trim($_POST['nombre']),
        'apellido' => trim($_POST['apellido']),
        'nombre_usuario' => trim($_POST['nombre_usuario']),
        'contrasena' => trim($_POST['contrasena']),
        'tipo_documento' => trim($_POST['tipo_documento']),
        'documento_identidad' => trim($_POST['documento_identidad']),
        'email' => trim($_POST['email']),
        'telefono' => trim($_POST['telefono'])
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
    <title>Registro - Lugyser</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <style>

html, body {
    margin: 0;
    padding: 0;
    height: auto;
    overflow-x: hidden;
    padding-bottom: 80px; /* Aumenta este valor según necesites */
}

        body {
            font-family: 'Roboto', sans-serif;
            background-color: #f8f9fa;
            color: #333;
        }
        .register-container {
            max-width: 600px;
            margin: 3rem auto;
            background-color: white;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .register-container h1 {
            font-size: 2.5rem;
            margin-bottom: 1.5rem;
            text-align: center;
            color: #28a745;
        }
        .form-control {
            margin-bottom: 1rem;
            border-radius: 5px;
        }
        .btn-primary {
            background-color: #28a745;
            border: none;
            border-radius: 5px;
            padding: 0.5rem 1rem;
        }
        .btn-primary:hover {
            background-color: #218838;
        }
        .text-center a {
            color: #007bff;
            text-decoration: none;
        }
        .text-center a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="register-container">
            <h1>Registrarse</h1>
            <?php echo $mensaje; ?>
            <form action="register.php" method="POST">
                <div class="form-group">
                    <label for="nombre">Nombre</label>
                    <input type="text" id="nombre" name="nombre" class="form-control" placeholder="Ingrese su nombre" required>
                </div>
                <div class="form-group">
                    <label for="apellido">Apellido</label>
                    <input type="text" id="apellido" name="apellido" class="form-control" placeholder="Ingrese su apellido" required>
                </div>
                <div class="form-group">
                    <label for="nombre_usuario">Nombre de Usuario</label>
                    <input type="text" id="nombre_usuario" name="nombre_usuario" class="form-control" placeholder="Ingrese un nombre de usuario" required>
                </div>
                <div class="form-group">
                    <label for="contrasena">Contraseña</label>
                    <input type="password" id="contrasena" name="contrasena" class="form-control" placeholder="Ingrese una contraseña" required>
                </div>
                <div class="form-group">
                    <label for="tipo_documento">Tipo de Documento</label>
                    <select id="tipo_documento" name="tipo_documento" class="form-control" required>
                        <option value="CC">Cédula de Ciudadanía</option>
                        <option value="TI">Tarjeta de Identidad</option>
                        <option value="CE">Cédula de Extranjería</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="documento_identidad">Número de Documento</label>
                    <input type="text" id="documento_identidad" name="documento_identidad" class="form-control" placeholder="Ingrese su número de documento" required>
                </div>
                <div class="form-group">
                    <label for="email">Correo Electrónico</label>
                    <input type="email" id="email" name="email" class="form-control" placeholder="Ingrese su correo electrónico" required>
                </div>
                <div class="form-group">
                    <label for="telefono">Teléfono</label>
                    <input type="text" id="telefono" name="telefono" class="form-control" placeholder="Ingrese su número de teléfono" required>
                </div>
                <button type="submit" class="btn btn-primary btn-block">Registrarse</button>
            </form>
            <p class="text-center mt-3">¿Ya tienes una cuenta? <a href="login.php">Inicia sesión aquí</a></p>
        </div>
    </div>
</body>
</html>
