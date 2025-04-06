<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['usuario']) || !in_array('administrador', array_column($_SESSION['roles'], 'nombre_rol'))) {
    header("Location: ../index.php");
    exit;
}

require_once '../config/database.php';

$db = new Database();
$conn = $db->getConnection();

// Obtener la lista de usuarios
$queryUsuarios = "SELECT idusuario, nombre, apellido, nombre_usuario FROM usuario";
$stmtUsuarios = $conn->prepare($queryUsuarios);
$stmtUsuarios->execute();
$usuarios = $stmtUsuarios->fetchAll(PDO::FETCH_ASSOC);

// Incluir el controlador para manejar la asignación de roles
include '../controllers/asignar_rol.php';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Asignar Rol</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #f8f9fa;
            color: #333;
        }
        .container {
            max-width: 600px;
            margin-top: 50px;
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        h1 {
            font-size: 2rem;
            margin-bottom: 20px;
            text-align: center;
            color: #28a745;
        }
        .btn-primary {
            background-color: #28a745;
            border: none;
        }
        .btn-primary:hover {
            background-color: #218838;
        }
        .alert {
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand" href="#">Asignar Rol</a>
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
    <div class="container">
        <h1>Asignar Rol</h1>
        <?php if (!empty($mensaje)) echo $mensaje; ?>
        <form method="POST" action="asignar_rol.php">
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
