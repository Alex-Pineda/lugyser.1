<?php
session_start();
require_once '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $db = new Database();
    $conn = $db->getConnection();

    $usuarioId = $_POST['usuario_id'];
    $nuevoRolId = $_POST['rol_id'];

    try {
        // Verificar si el usuario ya tiene el rol de proveedor asignado
        $query = "SELECT * FROM usuario_has_rol WHERE usuario_idusuario = :usuarioId AND rol_idrol = :nuevoRolId";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':usuarioId', $usuarioId, PDO::PARAM_INT);
        $stmt->bindParam(':nuevoRolId', $nuevoRolId, PDO::PARAM_INT);
        $stmt->execute();
        $rolExistente = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($rolExistente) {
            // Si ya existe la relación, actualizar el estado a 'activo'
            $query = "UPDATE usuario_has_rol 
                      SET estado = 'activo' 
                      WHERE usuario_idusuario = :usuarioId AND rol_idrol = :nuevoRolId";
            $stmt = $conn->prepare($query);
            $stmt->bindParam(':usuarioId', $usuarioId, PDO::PARAM_INT);
            $stmt->bindParam(':nuevoRolId', $nuevoRolId, PDO::PARAM_INT);
            $stmt->execute();
        } else {
            // Si no existe la relación, insertar un nuevo registro
            $query = "INSERT INTO usuario_has_rol (usuario_idusuario, rol_idrol, estado) 
                      VALUES (:usuarioId, :nuevoRolId, 'activo')";
            $stmt = $conn->prepare($query);
            $stmt->bindParam(':usuarioId', $usuarioId, PDO::PARAM_INT);
            $stmt->bindParam(':nuevoRolId', $nuevoRolId, PDO::PARAM_INT);
            $stmt->execute();
        }

        // Redirigir de vuelta a la página asignar_rol.php con un mensaje de éxito
        header('Location: ../views/admin_dashboard.php?mensaje=rol_asignado');
    } catch (PDOException $e) {
        error_log("Error al asignar rol: " . $e->getMessage());
        // Redirigir de vuelta a la página asignar_rol.php con un mensaje de error
        header('Location: ../views/asignar_rol.php?error=fallo');
    }
}
?>
