<?php
require_once '../models/RolModel.php';

$rolModel = new RolModel($conn);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $idUsuario = $_POST['usuario_id'];
    $nombreRol = $_POST['rol'];

    // Validar que el usuario y el rol existen
    $usuarioExiste = $rolModel->verificarUsuarioExiste($idUsuario);
    $rolExiste = $rolModel->verificarRolExiste($nombreRol);

    if ($usuarioExiste && $rolExiste) {
        $idRol = $rolExiste['idrol'];

        // Reemplazar todos los roles actuales por el nuevo rol
        $rolModel->asignarRolUnico($idUsuario, $idRol);

        // Redirigir para evitar reenvíos de formulario
        header("Location: admin_dashboard.php?mensaje=rol_asignado");
        exit;
    } else {
        echo "<div class='alert alert-danger text-center'>Error: Usuario o rol inválido.</div>";
    }
}
?>