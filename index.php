<?php
session_start();
require_once __DIR__ . '/models/UsuarioModel.php';
require_once __DIR__ . '/models/RolModel.php'; // Ajuste de la ruta
require_once __DIR__ . '/controllers/AuthController.php';
require_once __DIR__ . '/config/database.php'; // Archivo que configura la conexión a la base de datos


$db = new Database();
$usuarioModel = new UsuarioModel($db->getConnection());
$rolModel = new RolModel($db->getConnection());
$authController = new AuthController($usuarioModel, $rolModel);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre_usuario = isset($_POST['nombre_usuario']) ? $_POST['nombre_usuario'] : null;
    $contrasena = isset($_POST['contrasena']) ? $_POST['contrasena'] : null;

    if ($nombre_usuario && $contrasena) {
        $authController->login($nombre_usuario, $contrasena);
    } else {
        echo "<script>alert('Por favor, complete todos los campos.');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Página Principal - FincAntioquia</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="icon" href="/lugyser/favicon-rounded.ico" type="image/x-icon">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background-color:rgb(115, 205, 209);
            color: #333;
        }
        html, body {
            margin: 0;
            padding: 0;
            height: auto;
            overflow-x: hidden;
            padding-bottom: 80px; /* Aumenta este valor según necesites */
        }

        .navbar {
            margin-top: 30px;
            background-color: #28a745;
        }
        .navbar a {
            color: white;
            font-weight: bold;
        }

        .btn {
        color: #212529;
        background-color: #3d6083;
        border-color: #f8f9fa;
        }

        .hero {
            background-image: url('assets/images/hero.jpg'); /* Cambia esta ruta a tu imagen */
            background-size: cover;
            background-position: center;
            color: white;
            text-align: center;
            padding: 2rem 1rem;
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
        .mb-3 {
            border-radius: 50%; /* Hacer las imágenes circulares */
            width: 100px; /* Establecer un ancho fijo */
            height: 70px; /* Establecer un alto fijo */
            object-fit: cover; /* Ajustar la imagen dentro del contenedor */
        }

        @media (max-width: 768px) {
            .hero {
            padding: 2rem 1rem; /* Más compacto en pantallas pequeñas */
                }
        }

    </style>

</head>
<body>
    <!-- Barra de navegación -->
    <nav class="navbar navbar-expand-lg navbar-dark">
    <div class="container">
        <a class="navbar-brand" href="index.php">
        <img src="uploads/FA.jpeg" alt="Logo" width="40" height="40" style="border-radius: 50%;">
        </a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarContenido" aria-controls="navbarContenido" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        
        <div class="collapse navbar-collapse justify-content-end" id="navbarContenido">
            <?php if (!isset($_SESSION['usuario'])): ?>
                <a href="views/login.php" class="btn btn-light btn-sm mx-1 my-1">Iniciar Sesión</a>
                <a href="views/register.php" class="btn btn-light btn-sm mx-1 my-1">Registrarse</a>
            <?php else: ?>
                <a href="controllers/logout.php" class="btn btn-danger btn-sm mx-1 my-1">Cerrar Sesión</a>
            <?php endif; ?>
        </div>
    </div>

</nav>

    <!-- Hero Section -->
    <div class="hero">
        <h1>Bienvenido a FincAntioquia</h1>
        <p>Explora y reserva las mejores fincas para tus vacaciones.</p>
        <a href="views/listar_fincas.php" class="btn btn-primary btn-lg">Ver Lugares</a> <!-- Redirige correctamente a listar_fincas.php -->
    </div>

    <!-- Contenido adicional -->
    <div class="container mt-5">
        <h2 class="text-center">¿Por qué elegir FincAntioquia?</h2>
        <p class="text-center">Ofrecemos las mejores opciones para tus vacaciones, con fincas exclusivas y servicios de calidad.</p>
        <div class="row mt-4">
            <div class="col-md-4 text-center">
                <img src="https://st.depositphotos.com/1819777/1406/v/450/depositphotos_14060831-stock-illustration-sunset-on-the-beach.jpg" alt="Icono 1" class="mb-3" style="width: 80px;">
                <h4>Fincas Exclusivas</h4>
                <p>Encuentra las mejores fincas en ubicaciones privilegiadas.</p>
            </div>
            <div class="col-md-4 text-center">
                <img src="https://img.freepik.com/fotos-premium/icono-candado-ciberseguridad-concepto-red-digital-big-data_1034910-2403.jpg" alt="Icono 2" class="mb-3" style="width: 80px;">
                <h4>Reservas Seguras</h4>
                <p>Garantizamos la seguridad en todas tus reservas.</p>
            </div>
            <div class="col-md-4 text-center">
                <img src="https://blog.comparasoftware.com/wp-content/uploads/2021/02/dinamica-de-servicio-al-cliente-768x432.png" alt="Icono 3" class="mb-3" style="width: 80px;">
                <h4>Atención Personalizada</h4>
                <p>Estamos aquí para ayudarte en todo momento.</p>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>

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

        $success = $authController->register($datos);

        if ($success) {
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
        if ($_GET['action'] === 'mostrarMapa' && isset($_GET['idlugar'])) {
        $controller = new LugarController();
        $controller->mostrarMapaLugar($_GET['idlugar']);
    }
// Cerrar la conexión
$conn->close();

?>
