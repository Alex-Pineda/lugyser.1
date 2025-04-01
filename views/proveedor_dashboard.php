<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header("Location: ../views/login.php"); // Redirigir al formulario de inicio de sesión si no hay sesión
    exit;
}

// Verificar si el usuario tiene el rol de proveedor o administrador
$rolesPermitidos = ['proveedor', 'administrador'];
$esPermitido = in_array($_SESSION['roles'][0]['nombre_rol'], $rolesPermitidos);

if (!$esPermitido) {
    header("Location: ../index.php"); // Redirigir al inicio si no tiene permisos
    exit;
}

require_once '../config/database.php';

$db = new Database();
$conn = $db->getConnection();

// Verificar si la conexión a la base de datos es válida
if (!$conn) {
    die("Error al conectar con la base de datos.");
}

// Obtener la lista de lugares publicados por el proveedor que ha iniciado sesión
$idusuario = $_SESSION['usuario']['idusuario'];
error_log("ID del usuario en sesión: " . $idusuario);

$query = "SELECT idlugar, nombre_lugar, descripcion_lugar, imagen_lugar 
          FROM lugar 
          WHERE usuario_has_rol_usuario_idusuario = :idusuario";
$stmt = $conn->prepare($query);
$stmt->bindParam(':idusuario', $idusuario, PDO::PARAM_INT);

try {
    $stmt->execute();
    $lugares = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Depuración: Verificar si se obtuvieron resultados
    if (empty($lugares)) {
        error_log("No se encontraron lugares para el usuario con ID: $idusuario");
    } else {
        error_log("Se encontraron " . count($lugares) . " lugares para el usuario con ID: $idusuario");
    }
} catch (PDOException $e) {
    die("Error al obtener los lugares: " . $e->getMessage());
}

// Redirigir a la misma página después de publicar una finca
if (isset($_GET['publicado']) && $_GET['publicado'] === 'true') {
    echo "<script>alert('Finca publicada exitosamente.'); window.location.href='proveedor_dashboard.php';</script>";
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard del Proveedor - Lugyser</title>
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
        .container {
            margin-top: 2rem;
        }
        .card {
            margin-bottom: 1rem;
        }
    </style>
</head>
<body>
    <!-- Barra de navegación -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand" href="proveedor_dashboard.php">Lugyser - Proveedor</a>
            <div class="ml-auto">
                <a href="listar_fincas.php?proveedor_id=<?php echo $_SESSION['usuario']['idusuario']; ?>" class="btn btn-info btn-sm">Listar Fincas</a> <!-- Enlace actualizado -->
                <a href="../logout.php" class="btn btn-danger btn-sm">Cerrar Sesión</a>
            </div>
        </div>
    </nav>

    <div class="container">
        <h1 class="text-center">Dashboard del Proveedor</h1>
        <div class="text-center mb-4">
            <a href="publicar_finca.php" class="btn btn-primary">Publicar Nueva Finca</a> <!-- Botón accesible para ambos roles -->
            <a href="reservar_finca.php" class="btn btn-secondary">Realizar Reserva</a>
            <a href="listar_fincas.php?proveedor_id=<?php echo $_SESSION['usuario']['idusuario']; ?>" class="btn btn-info">Listar Fincas</a> <!-- Enlace actualizado -->
        </div>

        <h2 class="text-center">Tus Lugares Publicados</h2>
        <div class="row">
            <?php if (empty($lugares)): ?>
                <p class="text-center text-muted">No tienes lugares publicados. Publica tu primera finca <a href='publicar_finca.php'>aquí</a>.</p>
            <?php else: ?>
                <?php foreach ($lugares as $lugar): ?>
                    <div class="col-md-4">
                        <div class="card">
                            <img src="data:image/jpeg;base64,<?php echo base64_encode($lugar['imagen_lugar']); ?>" class="card-img-top" alt="Imagen del lugar">
                            <div class="card-body">
                                <h5 class="card-title"><?php echo htmlspecialchars($lugar['nombre_lugar']); ?></h5>
                                <p class="card-text"><?php echo htmlspecialchars($lugar['descripcion_lugar']); ?></p>
                                <a href="editar_lugar.php?id=<?php echo $lugar['idlugar']; ?>" class="btn btn-warning">Editar</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
