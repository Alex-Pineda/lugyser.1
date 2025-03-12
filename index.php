<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inicio</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        .card-title {
            font-size: 2.5rem;
            font-weight: bold;
        }
        .lead {
            font-size: 1.25rem;
        }
    </style>
</head>
<body class="bg-dark text-white">
    <div class="container mt-5">
        <div class="card bg-light text-dark">
            <div class="card-body">
                <h1 class="card-title text-center">Bienvenido a Lugyser</h1>
                <nav class="nav justify-content-center mb-4">
                    <a class="nav-link" href="views/listar_reservas.php">Ver Reservas</a>
                    <a class="nav-link" href="views/publicar_finca.php">Publicar Finca</a>
                    <a class="nav-link" href="views/listar_fincas.php">Ver Fincas Publicadas</a>
                </nav>
                <div class="text-center">
                    <p class="lead">Gestiona tus reservas y publicaciones de fincas de manera fácil y rápida.</p>
                </div>
            </div>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/5.3.0/js/bootstrap.min.js"></script>
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
        $result = $controller->register($_POST['nombre'], $_POST['email'], $_POST['password'], $_POST['telefono'], $_POST['direccion'], $_POST['ciudad'], $_POST['pais'], $_POST['codigo_postal']);
        if ($result) {
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
