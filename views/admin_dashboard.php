<?php
session_start();
if (!isset($_SESSION['usuario']) || !in_array('administrador', array_column($_SESSION['roles'], 'nombre_rol'))) {
    header("Location: ../index.php"); // Redirigir al nuevo index.php si no hay sesión o no tiene el rol de administrador
    exit;
}

require_once '../config/database.php';

$db = new Database();
$conn = $db->getConnection();

$usuarioNombre = $_SESSION['usuario']['nombre'];

// Obtener la lista de usuarios
$queryUsuarios = "SELECT idusuario, nombre, apellido, nombre_usuario FROM usuario";
$stmtUsuarios = $conn->prepare($queryUsuarios);
$stmtUsuarios->execute();
$usuarios = $stmtUsuarios->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Administrador</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #f8f9fa;
        }
        html, body {
    margin-right: 8px;
    padding: 0;
    height: auto;
    overflow-x: hidden;
    padding-bottom: 80px; /* Aumenta este valor según necesites */
}

        .navbar {
            background-color: #007bff;
            margin-top: 30px;
        }
        .navbar a {
            color: white;
            font-weight: bold;
        }
        .navbar .ml-auto a {
            margin-left: 10px;
        }
        .container h1 {
            color: #007bff;
            font-weight: bold;
        }
        .btn-primary {
            background-color: #007bff;
            border: none;
            margin-bottom: 15px; /* espacio entre botones */
        }
        .btn-primary:hover {
            background-color:rgb(28, 204, 192);
        }
        .btn-danger {
            background-color: #dc3545;
        }
        .btn-danger:hover {
            background-color: #c82333;
        }
    </style>
</head>
<body>
    <!-- Barra de navegación -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand" href="#">Administrador Dashboard</a>
            <div class="ml-auto">
                <?php if (isset($_SESSION['usuario'])): ?>
                    <a href="../controllers/logout.php" class="btn btn-danger btn-sm">Cerrar Sesión</a>
                <?php else: ?>
                    <a href="../views/login.php" class="btn btn-light btn-sm">Iniciar Sesión</a>
                    <a href="../views/register.php" class="btn btn-light btn-sm">Registrarse</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <div class="container mt-5">
        <h1 class="text-center">Panel de Administrador</h1>
        <div class="row text-center mt-4">
            <div class="col-md-3">
                <a href="publicar_finca.php" class="btn btn-primary btn-block">Publicar Finca</a>
            </div>
            <div class="col-md-3">
                <a href="reservar_finca.php" class="btn btn-primary btn-block">Reservar Finca</a>
            </div>
            <div class="col-md-3">
                <a href="listar_fincas.php" class="btn btn-primary btn-block">Listar Fincas</a>
            </div>
            <div class="col-md-3">
                <a href="listar_reservas.php" class="btn btn-primary btn-block">Listar Reservas</a>
            </div>
        </div>
        <h1 class="text-center mt-5">Asignar Roles</h1>
        <form method="POST" action="admin_dashboard.php" class="mt-4">
            <div class="form-group">
                <label for="usuario_id">Seleccionar Usuario</label>
                <select name="usuario_id" id="usuario_id" class="form-control" required>
                    <?php foreach ($usuarios as $usuario): ?>
                        <option value="<?php echo $usuario['idusuario']; ?>">
                            <?php echo $usuario['nombre'] . ' ' . $usuario['apellido'] . ' (' . $usuario['nombre_usuario'] . ')'; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group mt-3">
                <label for="rol">Seleccionar Rol</label>
                <select name="rol" id="rol" class="form-control" required>
                    <option value="administrador">Administrador</option>
                    <option value="proveedor">Proveedor</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary btn-block mt-4">Asignar Rol</button>
        </form>
    </div>
</body>
</html>
