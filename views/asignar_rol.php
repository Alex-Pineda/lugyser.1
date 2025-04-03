<?php
session_start();
if (!isset($_SESSION['usuario']) || !in_array('administrador', array_column($_SESSION['roles'], 'nombre_rol'))) {
    header("Location: ../views/login.php");
    exit;
}

require_once '../config/database.php';

$db = new Database();
$conn = $db->getConnection();

// Obtener la lista de usuarios que no tienen el rol de proveedor
$queryUsuarios = "
    SELECT u.idusuario, u.nombre, u.apellido, u.nombre_usuario 
    FROM usuario u
    LEFT JOIN usuario_has_rol ur ON u.idusuario = ur.usuario_idusuario AND ur.rol_idrol = 2
    WHERE ur.rol_idrol IS NULL
";
$stmtUsuarios = $conn->prepare($queryUsuarios);
$stmtUsuarios->execute();
$usuarios = $stmtUsuarios->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Asignar Rol Proveedor</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #f8f9fa;
            color: #333;
        }
        .container {
            margin-top: 2rem;
        }
        .card {
            margin-bottom: 1.5rem;
        }
        .card-title {
            font-size: 1.25rem;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1 class="text-center">Asignar Rol Proveedor</h1>

        <!-- Mostrar mensajes de éxito o error -->
        <?php if (isset($_GET['mensaje']) && $_GET['mensaje'] === 'rol_asignado'): ?>
            <div class="alert alert-success text-center">Rol asignado correctamente.</div>
        <?php elseif (isset($_GET['error']) && $_GET['error'] === 'fallo'): ?>
            <div class="alert alert-danger text-center">Error al asignar el rol. Inténtelo nuevamente.</div>
        <?php endif; ?>

        <div class="row">
            <?php if (!empty($usuarios)): ?>
                <?php foreach ($usuarios as $usuario): ?>
                    <div class="col-md-4 col-sm-6">
                        <div class="card shadow">
                            <div class="card-body text-center">
                                <h5 class="card-title"><?php echo htmlspecialchars($usuario['nombre'] . ' ' . $usuario['apellido']); ?></h5>
                                <p class="card-text">Usuario: <?php echo htmlspecialchars($usuario['nombre_usuario']); ?></p>
                                <form action="../controllers/asignar_rol.php" method="POST">
                                    <input type="hidden" name="usuario_id" value="<?php echo $usuario['idusuario']; ?>">
                                    <input type="hidden" name="rol_id" value="2"> <!-- Rol Proveedor -->
                                    <button type="submit" class="btn btn-primary">Asignar Rol Proveedor</button>
                                </form>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="text-center">No hay usuarios disponibles para asignar el rol de proveedor.</p>
            <?php endif; ?>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
