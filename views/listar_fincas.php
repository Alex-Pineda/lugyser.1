<?php
session_start();

require_once '../config/database.php';
require_once '../models/LugarModel.php';

$db = new Database();
$conn = $db->getConnection();
$lugarModel = new LugarModel($conn);

// Inicialización por defecto (visitante)
$esAdministrador = false;
$esProveedor = false;
$usuarioId = null;
$rolesUsuario = [];

// Si hay sesión activa y roles definidos
if (isset($_SESSION['roles']) && is_array($_SESSION['roles'])) {
    $rolesUsuario = array_column($_SESSION['roles'], 'nombre_rol');
    $esAdministrador = in_array('administrador', $rolesUsuario);
    $esProveedor = in_array('proveedor', $rolesUsuario);
    $usuarioId = $_SESSION['usuario']['idusuario'] ?? null;
}

if (isset($_GET['proveedor_id']) && is_numeric($_GET['proveedor_id'])) {
    $lugares = $lugarModel->obtenerLugaresConUsuarioYRol($_GET['proveedor_id'], 'proveedor');
} elseif ($esAdministrador) {
    $lugares = $lugarModel->obtenerTodosLosLugares();
} elseif ($usuarioId !== null && $esProveedor) {
    $lugares = $lugarModel->obtenerLugaresConUsuarioYRol($usuarioId, 'proveedor');
} else {
    $lugares = $lugarModel->obtenerTodosLosLugares();
}
?>
<?php
include_once __DIR__ . '/../includes/header.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fincas Publicadas</title>
    <link rel="stylesheet" href="../css/Estilos/listar_fincas.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="icon" href="/lugyser/favicon-rounded.ico" type="image/x-icon">

    <style>
        html, body {
            margin: 0;
            padding: 0;
            height: auto;
            overflow-x: hidden;
            padding-bottom: 100px;
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
            gap: 10px;
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
            height: 50px;
            color: aliceblue;
        }
    </style>
</head>
<body class="bg-dark text-white">
    <div class="container mt-5">
        <h1 class="text-center">Listado de Fincas</h1>
        <form method="GET" class="mb-4">
            <div class="input-group">
                <input type="text" name="buscar" class="form-control" placeholder="Buscar finca por nombre..." value="<?php echo isset($_GET['buscar']) ? htmlspecialchars($_GET['buscar']) : ''; ?>">
                <div class="input-group-append">
                    <button class="btn btn-primary" type="submit">Buscar</button>
                </div>
            </div>
        </form>
        <?php
        if (isset($_GET['buscar']) && trim($_GET['buscar']) !== '') {
            $busqueda = trim($_GET['buscar']);
            $lugares_filtrados = array_filter($lugares, function($lugar) use ($busqueda) {
                return stripos($lugar['nombre_lugar'], $busqueda) !== false;
            });
        } else {
            $lugares_filtrados = $lugares;
        }
        ?>
        <div class="row row-equal-height">
            <?php if (!empty($lugares_filtrados)): ?>
                <?php foreach ($lugares_filtrados as $lugar): ?>
                    <div class="col-md-4 col-equal-height">
                        <div class="card bg-light text-dark shadow">
                            <img src="data:image/jpeg;base64,<?php echo base64_encode($lugar['imagen_lugar']); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($lugar['nombre_lugar']); ?>">
                            <div class="card-body">
                                <h5 class="card-title"><?php echo htmlspecialchars($lugar['nombre_lugar']); ?></h5>
                                <p class="card-text"><?php echo htmlspecialchars($lugar['descripcion_lugar']); ?></p>
                                <?php if ($esAdministrador || $esProveedor || ($usuarioId !== null && isset($lugar['idusuario']) && $lugar['idusuario'] == $usuarioId)): ?>
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
                    <?php
                    if (isset($_GET['buscar']) && trim($_GET['buscar']) !== '') {
                        echo "Lugar no encontrado";
                    } else {
                        echo $esProveedor ? "No tienes lugares publicados. <a href='publicar_finca.php' class='btn btn-primary btn-sm'>Publicar Finca</a>" : "No hay lugares disponibles.";
                    }
                    ?>
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
