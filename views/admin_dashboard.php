<?php
session_start();
if (!isset($_SESSION['usuario']) || !in_array('administrador', array_column($_SESSION['roles'], 'nombre_rol'))) {
    header("Location: ../views/login.php");
    exit;
}

require_once '../config/database.php'; // Asegúrate de incluir correctamente la configuración de la base de datos

$db = new Database();
$conn = $db->getConnection(); // Inicializa correctamente la conexión a la base de datos

echo "Bienvenido, Administrador: " . $_SESSION['usuario']['nombre'];

$roles = $_SESSION['roles'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['usuario_id'], $_POST['rol'])) {
    $usuario_id = $_POST['usuario_id'];
    $rol = $_POST['rol'];

    // Asignar el rol al usuario
    $query = "INSERT INTO usuario_has_rol (usuario_idusuario, rol_idrol, estado)
              VALUES (:usuario_id, (SELECT idrol FROM rol WHERE nombre_rol = :rol), 'activo')
              ON DUPLICATE KEY UPDATE estado = 'activo'";
    $stmt = $conn->prepare($query); // Ahora `$conn` está correctamente definido
    $stmt->bindParam(':usuario_id', $usuario_id);
    $stmt->bindParam(':rol', $rol);
    if ($stmt->execute()) {
        echo "<div class='alert alert-success'>Rol asignado correctamente.</div>";
    } else {
        echo "<div class='alert alert-danger'>Error al asignar el rol.</div>";
    }
}

// Obtener la lista de usuarios
$queryUsuarios = "SELECT idusuario, nombre, apellido, nombre_usuario FROM usuario";
$stmtUsuarios = $conn->prepare($queryUsuarios); // Ahora `$conn` está correctamente definido
$stmtUsuarios->execute();
$usuarios = $stmtUsuarios->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Administración</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #f8f9fa;
            color: #333;
        }
        .dashboard-header {
            background-color: #343a40;
            color: white;
            padding: 1.5rem 0;
            text-align: center;
        }
        .dashboard-header h1 {
            font-size: 2.5rem;
        }
        .dashboard-content {
            padding: 2rem;
        }
        .card {
            margin-bottom: 1.5rem;
        }
        .card i {
            font-size: 3rem;
            color: #007bff;
        }
        .card-title {
            font-size: 1.5rem;
            margin-top: 1rem;
        }
        @media (max-width: 768px) {
            .dashboard-header h1 {
                font-size: 2rem;
            }
            .card i {
                font-size: 2.5rem;
            }
            .card-title {
                font-size: 1.25rem;
            }
        }
        @media (max-width: 576px) {
            .dashboard-header h1 {
                font-size: 1.8rem;
            }
            .card i {
                font-size: 2rem;
            }
            .card-title {
                font-size: 1rem;
            }
        }
    </style>
</head>
<body>
    <header class="dashboard-header d-flex justify-content-between align-items-center">
        <h1>Panel de Administración</h1>
        <form action="../index.php" method="POST" style="margin-right: 20px;">
            <button type="submit" class="btn btn-danger">Cerrar Sesión</button>
        </form>
    </header>

    <main class="dashboard-content container">
        <div class="row">
            <div class="col-md-4 col-sm-6">
                <div class="card text-center shadow">
                    <div class="card-body">
                        <i class="fas fa-home"></i>
                        <h5 class="card-title">Fincas</h5>
                        <p class="card-text">Gestiona las fincas publicadas por los proveedores.</p>
                        <a href="../views/listar_fincas.php" class="btn btn-primary">Ver Fincas</a>
                    </div>
                </div>
            </div>
            <div class="col-md-4 col-sm-6">
                <div class="card text-center shadow">
                    <div class="card-body">
                        <i class="fas fa-calendar-alt"></i>
                        <h5 class="card-title">Reservas</h5>
                        <p class="card-text">Gestiona las reservas realizadas por los clientes.</p>
                        <a href="../views/listar_reservas.php" class="btn btn-primary">Ver Reservas</a>
                    </div>
                </div>
            </div>
            <div class="col-md-4 col-sm-6">
                <div class="card text-center shadow">
                    <div class="card-body">
                        <i class="fas fa-plus-circle"></i>
                        <h5 class="card-title">Publicar Finca</h5>
                        <p class="card-text">Publica nuevas fincas para los clientes.</p>
                        <a href="../views/publicar_finca.php" class="btn btn-primary">Publicar</a>
                    </div>
                </div>
            </div>
            <div class="col-md-4 col-sm-6">
                <div class="card text-center shadow">
                    <div class="card-body">
                        <i class="fas fa-book"></i>
                        <h5 class="card-title">Reservar Finca</h5>
                        <p class="card-text">Reserva una finca para un cliente.</p>
                        <a href="../views/reservar_finca.php" class="btn btn-primary">Reservar</a>
                    </div>
                </div>
            </div>
            <div class="col-md-4 col-sm-6">
                <div class="card text-center shadow">
                    <div class="card-body">
                        <i class="fas fa-user-tag"></i>
                        <h5 class="card-title">Asignar Rol Proveedor</h5>
                        <p class="card-text">Asigna el rol de proveedor a un usuario cliente.</p>
                        <a href="../views/asignar_rol.php" class="btn btn-primary">Asignar Rol</a>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <footer class="text-center py-3 bg-dark text-white">
        <p>&copy; 2025 Lugyser. Todos los derechos reservados.</p>
    </footer>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
