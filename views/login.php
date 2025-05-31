<?php
session_start();
require_once '../config/database.php';
require_once '../models/UsuarioModel.php';
require_once '../models/RolModel.php';
require_once '../controllers/AuthController.php';

$db = new Database();
$usuarioModel = new UsuarioModel($db->getConnection());
$rolModel = new RolModel($db->getConnection());
$authController = new AuthController($usuarioModel, $rolModel);

$mensaje = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre_usuario = $_POST['nombre_usuario'];
    $contrasena = $_POST['contrasena'];
    $usuario = $usuarioModel->autenticarUsuario($nombre_usuario, $contrasena);

    if ($usuario) {
        $_SESSION['usuario'] = $usuario;
        $_SESSION['usuario_id'] = $usuario['idusuario'];
        $_SESSION['roles'] = $rolModel->obtenerRolesPorUsuario($usuario['idusuario']);
        foreach ($_SESSION['roles'] as $rol) {
            if ($rol['nombre_rol'] === 'administrador') {
                header("Location: ../views/admin_dashboard.php"); // Redirigir al panel de administrador
                exit;
            } elseif ($rol['nombre_rol'] === 'proveedor') {
                header("Location: ../views/proveedor_dashboard.php"); // Redirigir al dashboard de proveedor
                exit;
            } elseif ($rol['nombre_rol'] === 'cliente') {
                header("Location: ../views/reservar_finca.php"); // Redirigir al dashboard de cliente
                exit;
            }
        }
        header("Location: ../index.php"); // Redirigir al inicio si no tiene un rol válido
        exit;
    } else {
        $mensaje = 'Credenciales incorrectas. Por favor, inténtelo de nuevo.';
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión -  FincAntioquia</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="icon" href="/lugyser/favicon-rounded.ico" type="image/x-icon">

</head>
<body>
    <div class="container mt-5">
        <h1 class="text-center">Iniciar Sesión</h1>
        <?php if (!empty($mensaje)): ?>
            <div class="alert alert-danger"><?php echo $mensaje; ?></div>
        <?php endif; ?>
        <form method="POST" action="login.php" class="mt-4 d-flex flex-column align-items-center">
            <div class="form-group w-100" style="max-width: 350px;">
                <label for="nombre_usuario" class="w-100 text-center">Nombre de Usuario</label>
                <input type="text" name="nombre_usuario" id="nombre_usuario" class="form-control" placeholder="Usuario" required style="max-width: 450px; margin: 0 auto;">
            </div>
            <div class="form-group w-100" style="max-width: 350px;">
                <label for="contrasena" class="w-100 text-center">Contraseña</label>
                <input type="password" name="contrasena" id="contrasena" class="form-control" placeholder="Contraseña" required style="max-width: 450px; margin: 0 auto;">
            </div>
            <button type="submit" class="btn btn-primary btn-block" style="max-width: 350px; width: 100%;">Iniciar Sesión</button>
        </form>
        <div class="text-center mt-3">
            <a href="../index.php" class="btn btn-secondary" style="max-width: 300px; width: 100%;">Volver a la Página Principal</a>
        </div>
    </div>
</body>
</html>
<?php
// Incluir pie de página
include '../includes/footer.php';
?>
