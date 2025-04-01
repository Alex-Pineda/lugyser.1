<?php
session_start();
if (isset($_SESSION['usuario'])) {
    header("Location: admin_dashboard.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inicio de Sesión - Lugyser</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #f8f9fa;
            color: #333;
        }
        .hero {
            background: url('../images/paisaje.jpg') no-repeat center center/cover; /* Imagen de paisaje */
            height: 70vh;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.7);
        }
        .hero h1 {
            font-size: 4rem;
            font-weight: bold;
        }
        .features {
            padding: 3rem 0;
        }
        .features .feature {
            text-align: center;
            padding: 1.5rem;
        }
        .features .feature i {
            font-size: 4rem;
            color: #28a745;
        }
        .features .feature h3 {
            margin-top: 1rem;
            font-size: 1.75rem;
        }
        .login-container {
            background-color: white;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            text-align: center;
            margin: 3rem auto;
            max-width: 400px;
        }
        .login-container h1 {
            font-size: 2rem;
            margin-bottom: 1.5rem;
        }
        .form-control {
            margin-bottom: 1rem;
        }
        .btn-register {
            margin-top: 1rem;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            padding: 0.5rem 1rem;
            border-radius: 5px;
            display: inline-block;
        }
        .btn-register:hover {
            background-color: #0056b3;
        }
        footer {
            background-color: #343a40;
            color: white;
            padding: 1.5rem 0;
            text-align: center;
        }
    </style>
</head>
<body>
    <!-- Hero Section -->
    <div class="hero">
        <h1>Bienvenido a Lugyser</h1>
    </div>

    <!-- Features Section -->
    <div class="container features">
        <div class="row">
            <div class="col-md-4 feature">
                <i class="fas fa-home"></i>
                <h3>Fincas de Ensueño</h3>
                <p>Encuentra las mejores fincas para tus vacaciones.</p>
            </div>
            <div class="col-md-4 feature">
                <i class="fas fa-calendar-alt"></i>
                <h3>Reservas Fáciles</h3>
                <p>Reserva tu lugar favorito en pocos pasos.</p>
            </div>
            <div class="col-md-4 feature">
                <i class="fas fa-users"></i>
                <h3>Atención Personalizada</h3>
                <p>Estamos aquí para ayudarte en todo momento.</p>
            </div>
        </div>
    </div>

    <!-- Login Section -->
    <div class="login-container">
        <h1>Iniciar Sesión</h1>
        <form action="../controllers/AuthController.php" method="POST">
            <input type="text" name="nombre_usuario" placeholder="Usuario" required class="form-control">
            <input type="password" name="contrasena" placeholder="Contraseña" required class="form-control">
            <button type="submit" class="btn btn-success btn-block">Iniciar Sesión</button>
        </form>
        <a href="register.php" class="btn-register">Registrarse como Proveedor</a>
    </div>

    <!-- Footer -->
    <footer>
        <p>&copy; 2023 Lugyser. Todos los derechos reservados.</p>
    </footer>

    <!-- Bootstrap JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
