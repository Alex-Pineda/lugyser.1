<?php
session_start();
if (!isset($_SESSION['usuario']) || !in_array('proveedor', array_column($_SESSION['roles'], 'nombre_rol'))) {
    header("Location: ../index.php"); // Redirigir al nuevo index.php si no hay sesión o no tiene el rol de proveedor
    exit;
}

require_once '../config/database.php';
require_once '../models/LugarModel.php';

$db = new Database();
$conn = $db->getConnection();
$lugarModel = new LugarModel($conn);

// Obtener el ID del usuario en sesión
$usuarioId = $_SESSION['usuario']['idusuario'];

// Obtener los lugares publicados por el proveedor
$lugares = $lugarModel->obtenerLugaresConUsuarioYRol($usuarioId, 'proveedor');

if (!$lugares) {
    error_log("No se encontraron fincas publicadas para el usuario con ID {$usuarioId}.");
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Proveedor</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="icon" href="/lugyser/favicon-rounded.ico" type="image/x-icon">

    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #f8f9fa;
        }
        html, body {
    margin: 0;
    padding: 0;
    height: auto;
    overflow-x: hidden;
    padding-bottom: 80px; /* Aumenta este valor según necesites */
}
        .navbar {
            background-color: #28a745;
            margin-top: 30px;
        }
        .navbar a {
            color: white;
            font-weight: bold;
        }
        .navbar .ml-auto a {
            margin-left: 10px;
        }
        .card-img-top {
            width: 100%;
            height: 200px;
            object-fit: cover;
        }
        .card {
            height: 100%;
            display: flex;
            flex-direction: column;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .card-body {
            display: flex;
            flex-direction: column;
            flex-grow: 1;
        }
        .btn-group {
            display: flex;
            justify-content: space-between;
        }
        .btn {
            margin-bottom: 15px; /* espacio entre botones */
            display: block;
            margin: 10px auto; /* Los centra automáticamente horizontalmente */
            width: fit-content; /* Hace que el ancho del botón se ajuste al contenido */

        }

        .container h1 {
            color: #28a745;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <!-- Barra de navegación -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand" href="#">Bienvenido, <?php echo htmlspecialchars($_SESSION['usuario']['nombre']); ?></a>

            <div class="ml-auto">
                <a href="../controllers/logout.php" class="btn btn-danger btn-sm">Cerrar Sesión</a>
            </div>
        </div>
    </nav>

    <div class="container mt-5">
        <div class="row text-center mt-4">
            <div class="col-md-6">
                <a href="publicar_finca.php" class="btn btn-primary btn-block">Publicar Finca</a>
            </div>
            <div class="col-md-6">
                <a href="reservar_finca.php" class="btn btn-primary btn-block">Reservar Finca</a>
            </div>
        </div>
        <div class="row mt-5">
            <?php if (!empty($lugares)): ?>
                <?php foreach ($lugares as $lugar): ?>
                    <div class="col-md-4 mb-4">
                        <div class="card">
                            <img src="data:image/jpeg;base64,<?php echo base64_encode($lugar['imagen_lugar']); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($lugar['nombre_lugar']); ?>">
                            <div class="card-body">
                                <h5 class="card-title"><?php echo htmlspecialchars($lugar['nombre_lugar']); ?></h5>
                                <p class="card-text"><?php echo htmlspecialchars($lugar['descripcion_lugar']); ?></p>
                                <div class="btn-group">
                                    <a href="editar_lugar.php?id=<?php echo $lugar['idlugar']; ?>" class="btn btn-warning btn-sm">Editar</a>
                                    <a href="../controllers/eliminar_lugar.php?id=<?php echo $lugar['idlugar']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('¿Está seguro de que desea eliminar este lugar?');">Eliminar</a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="text-center">No tienes lugares publicados.</p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
<?php
// Incluir pie de página
include '../includes/footer.php';
?>
