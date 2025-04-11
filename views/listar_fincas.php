<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header("Location: ../index.php"); // Redirigir al nuevo index.php si no hay sesión
    exit;
}

// Permitir acceso a proveedores y administradores
$rolesPermitidos = ['proveedor', 'administrador'];
$esAdministrador = in_array('administrador', array_column($_SESSION['roles'], 'nombre_rol'));
$esProveedor = in_array('proveedor', array_column($_SESSION['roles'], 'nombre_rol'));

if (!in_array($_SESSION['roles'][0]['nombre_rol'], $rolesPermitidos)) {
    header("Location: ../index.php"); // Redirigir al nuevo index.php si no tiene permisos
    exit;
}

require_once '../config/database.php';
require_once '../models/LugarModel.php';

$db = new Database();
$conn = $db->getConnection();
$lugarModel = new LugarModel($conn);

// Validar que `idusuario` exista antes de usarlo
$usuarioId = isset($_SESSION['usuario']['idusuario']) ? $_SESSION['usuario']['idusuario'] : null;

if (isset($_GET['proveedor_id']) && is_numeric($_GET['proveedor_id'])) {
    error_log("Proveedor ID recibido: " . $_GET['proveedor_id']);
    $lugares = $lugarModel->obtenerLugaresConUsuarioYRol($_GET['proveedor_id'], 'proveedor');
} elseif ($esAdministrador) {
    $lugares = $lugarModel->obtenerTodosLosLugares();
} elseif ($usuarioId !== null) {
    error_log("ID del usuario en sesión: " . $usuarioId);
    $lugares = $lugarModel->obtenerLugaresConUsuarioYRol($usuarioId, 'proveedor');
} else {
    $lugares = [];
}

// Depuración: Registrar los resultados obtenidos
error_log("Lugares obtenidos: " . json_encode($lugares));
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fincas Publicadas</title>
    <link rel="stylesheet" href="../css/Estilos/listar_fincas.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        html, body {
    margin: 0;
    padding: 0;
    height: auto;
    overflow-x: hidden;
    padding-bottom: 100px; /* Aumenta este valor según necesites */
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
        }
        .card-body {
            display: flex;
            flex-direction: column;
            flex-grow: 1;
        }
        .card-title {
            font-size: 1.25rem;
            font-weight: bold;
        }
        .card-text {
            flex-grow: 1;
        }
        .btn-group {
            display: flex;
            justify-content: space-between;
            gap: 10px; /* Generar un pequeño espacio entre los botones */
        }
        .row-equal-height {
            display: flex;
            flex-wrap: wrap;
        }
        .col-equal-height {
            display: flex;
            flex-direction: column;
            margin-bottom: 1rem;
        }
        h1.text-center {
            height: 80px; /* Estilo solicitado */
            color: aliceblue; /* Estilo solicitado */
        }
    </style>
</head>
<body class="bg-dark text-white">
    <div class="container mt-5">
        <h1 class="text-center">Listado de Fincas</h1>
        <div class="row row-equal-height">
            <?php if (!empty($lugares)): ?>
                <?php foreach ($lugares as $lugar): ?>
                    <div class="col-md-4 col-equal-height">
                        <div class="card bg-light text-dark shadow">
                            <img src="data:image/jpeg;base64,<?php echo base64_encode($lugar['imagen_lugar']); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($lugar['nombre_lugar']); ?>">
                            <div class="card-body">
                                <h5 class="card-title"><?php echo htmlspecialchars($lugar['nombre_lugar']); ?></h5>
                                <p class="card-text"><?php echo htmlspecialchars($lugar['descripcion_lugar']); ?></p>
                                <?php if ($esAdministrador || ($usuarioId !== null && $lugar['idusuario'] == $usuarioId)): ?>
                                    <div class="btn-group">
                                        <a href="editar_lugar.php?id=<?php echo $lugar['idlugar']; ?>" class="btn btn-warning btn-sm">Editar</a>
                                        <a href="eliminar_lugar.php?id=<?php echo $lugar['idlugar']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('¿Está seguro de que desea eliminar este lugar?');">Eliminar</a>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="text-center">
                    <?php echo $esProveedor ? "No tienes lugares publicados. <a href='publicar_finca.php' class='btn btn-primary btn-sm'>Publicar Finca</a>" : "No hay lugares disponibles."; ?>
                </p>
            <?php endif; ?>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>

<?php
// Incluir pie de página
include '../includes/footer.php';
?>