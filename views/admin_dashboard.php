<?php
session_start();
if (!isset($_SESSION['usuario'])) { // Verifica que la sesión esté iniciada
    header("Location: ../index.php"); // Redirigir al index si no hay sesión
    exit;
}
if (!in_array('administrador', array_column($_SESSION['roles'], 'nombre_rol'))) { // Verifica que el usuario tenga el rol de administrador
    header("Location: ../index.php"); // Redirigir al index si no tiene el rol de administrador
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
    <title>Panel de Administrador - Lugyser</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #f8f9fa;
            color: #333;
        }
        .hero {
            background-color: #28a745;
            color: white;
            padding: 2rem 0;
            text-align: center;
        }
        .hero h1 {
            font-size: 3rem;
            font-weight: bold;
        }
        .options-container {
            margin-top: 2rem;
            text-align: center;
        }
        .options-container a {
            margin: 10px;
            font-size: 1.2rem;
            color: white;
            background-color: #28a745;
            padding: 10px 20px;
            border-radius: 5px;
            text-decoration: none;
            font-weight: bold;
        }
        .options-container a:hover {
            background-color: #218838;
        }
    </style>
</head>
<body>
    <div class="hero">
        <h1>Panel de Administrador</h1>
    </div>

    <div class="container options-container">
        <!-- Opciones del panel de administrador -->
        <a href="publicar_finca.php">Publicar Finca</a>
        <a href="reservar_finca.php">Reservar Finca</a>
        <a href="listar_fincas.php">Listar Fincas</a>
        <a href="listar_reservas.php">Listar Reservas</a>
        <a href="../logout.php" class="btn btn-danger">Cerrar Sesión</a>
    </div>

    <div class="container mt-5">
        <h2 class="text-center">Asignar Roles</h2>
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
            <button type="submit" class="btn btn-primary mt-3">Asignar Rol</button>
        </form>
    </div>

    <footer class="text-center mt-5">
        <p>&copy; 2023 Lugyser. Todos los derechos reservados.</p>
    </footer>
</body>
</html>
