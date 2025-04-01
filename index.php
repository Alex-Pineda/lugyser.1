<?php
session_start();
require_once './models/UsuarioModel.php';
require_once './models/RolModel.php';
require_once './controllers/AuthController.php';
require_once './config/database.php'; // Archivo que configura la conexión a la base de datos

$db = new Database();
$usuarioModel = new UsuarioModel($db->getConnection());
$rolModel = new RolModel($db->getConnection());
$authController = new AuthController($usuarioModel, $rolModel);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre_usuario = $_POST['nombre_usuario'];
    $contrasena = $_POST['contrasena'];
    $authController->login($nombre_usuario, $contrasena);
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Página Principal - Lugyser</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #f8f9fa;
            color: #333;
        }
        .navbar {
            background-color: #28a745;
        }
        .navbar a {
            color: white;
            font-weight: bold;
        }
        .hero {
            background-image: url('assets/images/hero.jpg'); /* Cambia esta ruta a tu imagen */
            background-size: cover;
            background-position: center;
            color: white;
            text-align: center;
            padding: 5rem 1rem;
        }
        .hero h1 {
            font-size: 3rem;
            font-weight: bold;
        }
        .hero p {
            font-size: 1.5rem;
            margin-top: 1rem;
        }
        .btn-primary {
            background-color: #28a745;
            border: none;
        }
        .btn-primary:hover {
            background-color: #218838;
        }
    </style>
</head>
<body>
    <!-- Barra de navegación -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand" href="index.php">Lugyser</a>
            <div class="ml-auto">
                <?php if (!isset($_SESSION['usuario'])): ?>
                    <a href="views/login.php" class="btn btn-light btn-sm">Iniciar Sesión</a> <!-- Redirige correctamente a login.php -->
                    <a href="views/register.php" class="btn btn-light btn-sm">Registrarse</a>
                <?php else: ?>
                    <a href="logout.php" class="btn btn-danger btn-sm">Cerrar Sesión</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <div class="hero">
        <h1>Bienvenido a Lugyser</h1>
        <p>Explora y reserva las mejores fincas para tus vacaciones.</p>
        <a href="views/reservar_finca.php" class="btn btn-primary btn-lg">Descubre Lugares</a> <!-- Redirige correctamente a reservar_finca.php -->
        <?php if (isset($_SESSION['usuario'])): ?>
            <a href="views/reservar_finca.php" class="btn btn-primary btn-lg">Reservar Finca</a>
        <?php endif; ?>
    </div>

    <!-- Contenido adicional -->
    <div class="container mt-5">
        <h2 class="text-center">¿Por qué elegir Lugyser?</h2>
        <p class="text-center">Ofrecemos las mejores opciones para tus vacaciones, con fincas exclusivas y servicios de calidad.</p>
        <div class="row mt-4">
            <div class="col-md-4 text-center">
                <img src="assets/images/icon1.png" alt="Icono 1" class="mb-3" style="width: 80px;">
                <h4>Fincas Exclusivas</h4>
                <p>Encuentra las mejores fincas en ubicaciones privilegiadas.</p>
            </div>
            <div class="col-md-4 text-center">
                <img src="assets/images/icon2.png" alt="Icono 2" class="mb-3" style="width: 80px;">
                <h4>Reservas Seguras</h4>
                <p>Garantizamos la seguridad en todas tus reservas.</p>
            </div>
            <div class="col-md-4 text-center">
                <img src="assets/images/icon3.png" alt="Icono 3" class="mb-3" style="width: 80px;">
                <h4>Atención Personalizada</h4>
                <p>Estamos aquí para ayudarte en todo momento.</p>
            </div>
        </div>
    </div>
</body>
</html>
<?php
require_once 'controllers/UsuarioController.php';
require_once 'controllers/LugarController.php';
require_once 'controllers/ReservaController.php';

// Conectar a la base de datos
$conn = new mysqli('localhost', 'root', '', 'lugyser');

// Verificar la conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

$action = $_GET['action'] ?? '';

switch ($action) {
    case 'login':
        $controller = new UsuarioController();
        $result = $controller->login($_POST['nombre'], $_POST['password']);
        if ($result) {
            header('Location: ../views/dashboard.php');
        } else {
            echo "Error en el inicio de sesión.";
        }
        break;
    case 'register':
        $controller = new UsuarioController();
        $success = $controller->register($_POST['nombre'], $_POST['email'], $_POST['password'], $_POST['telefono'], $_POST['direccion'], $_POST['ciudad'], $_POST['pais'], $_POST['codigo_postal']);
        if ($success === null) {
            $success = false; // Handle cases where the function might still return void
        }
        if ($success !== false) {
            header('Location: ../views/login.php');
        } else {
            echo "Error en el registro.";
        }
        break;
    case 'registerLugar':
        $controller = new LugarController();
        $result = $controller->createLugar([
            'nombre_lugar' => $_POST['nombre_lugar'],
            'imagen_lugar' => file_get_contents($_FILES['imagen_lugar']['tmp_name']),
            'tipo' => $_POST['tipo'],
            'ubicacion_lugar' => $_POST['ubicacion_lugar'],
            'descripcion_lugar' => $_POST['descripcion_lugar'],
            'cantidad_habitaciones' => $_POST['cantidad_habitaciones'],
            'disponibilidad_lugar' => isset($_POST['disponibilidad_lugar']) ? 1 : 0,
            'precio_lugar' => $_POST['precio_lugar'],
            'usuario_idusuario' => $_POST['usuario_idusuario'],
            'rol_idrol' => $_POST['rol_idrol'],
            'cantidad_banos' => $_POST['cantidad_banos'],
            'cantidad_piscinas' => $_POST['cantidad_piscinas'],
            'juegos_infantiles' => $_POST['juegos_infantiles'],
            'zonas_verdes' => $_POST['zonas_verdes']
        ]);
        if ($result) {
            header('Location: ../views/detalle_lugar.php?id=' . $result);
        } else {
            echo "Error en el registro del lugar.";
        }
        break;
    case 'viewReservations':
        $controller = new ReservaController();
        $controller->showReservas();
        break;
    case 'registerReserva':
        // Obtener los datos del formulario
        $data = [
            'fecha_reserva' => $_POST['fecha_reserva'] ?? date("Y-m-d"),
            'nombre_cliente' => $_POST['nombre_cliente'] ?? 'Desconocido',
            'fecha_inicio' => $_POST['fecha_inicio'] ?? null,
            'fecha_final' => $_POST['fecha_final'] ?? null,
            'cantidad_personas' => $_POST['cantidad_personas'] ?? 0,
            'metodo_pago' => $_POST['metodo_pago'] ?? 'Efectivo',
            'estado_reserva' => $_POST['estado_reserva'] ?? 'Pendiente'
        ];

        // Validar que todos los datos necesarios estén presentes
        if (empty($data['nombre_cliente']) || empty($data['fecha_inicio']) || empty($data['fecha_final']) || empty($data['cantidad_personas']) || 
            empty($data['estado_reserva']) || empty($data['metodo_pago'])) {
            echo "Error: Datos insuficientes para realizar la reserva. Por favor, asegúrese de que todos los campos estén completos.";
            break;
        }

        // Insertar los datos en la base de datos
        $controller = new ReservaController();
        $result = $controller->createReserva($data);

        if ($result) {
            echo "Reserva registrada exitosamente.";
        } else {
            echo "Error al registrar la reserva.";
        }
        break;
}
// Cerrar la conexión
$conn->close();
?>