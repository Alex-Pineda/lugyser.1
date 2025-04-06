<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start(); // Inicia la sesión solo si no está activa
}

require_once '../config/database.php';

if (!isset($_SESSION['usuario']) || !in_array('administrador', array_column($_SESSION['roles'], 'nombre_rol'))) {
    header("Location: ../index.php");
    exit;
}

$mensaje = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $db = new Database();
    $conn = $db->getConnection();

    $usuario_id = intval($_POST['usuario_id']);
    $rol = trim($_POST['rol']);

    try {
        // Asignar el rol al usuario
        $query = "INSERT INTO usuario_has_rol (usuario_idusuario, rol_idrol, estado)
                  VALUES (:usuario_id, (SELECT idrol FROM rol WHERE nombre_rol = :rol), 'activo')
                  ON DUPLICATE KEY UPDATE estado = 'activo'";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':usuario_id', $usuario_id, PDO::PARAM_INT);
        $stmt->bindParam(':rol', $rol, PDO::PARAM_STR);

        if ($stmt->execute()) {
            $mensaje = '<div class="alert alert-success text-center">Rol asignado exitosamente.</div>';
        } else {
            $mensaje = '<div class="alert alert-danger text-center">No se pudo asignar el rol. Por favor, inténtelo de nuevo.</div>';
        }
    } catch (PDOException $e) {
        error_log("Error al asignar rol: " . $e->getMessage());
        $mensaje = '<div class="alert alert-danger text-center">No se pudo asignar el rol. Por favor, inténtelo de nuevo.</div>';
    }
}
?>
