<?php
class UsuarioModel {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function obtenerUsuarioPorId($idusuario) {
        $query = "SELECT * FROM usuario WHERE idusuario = ?";
        $stmt = $this->db->prepare($query);
        $stmt->execute([$idusuario]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function autenticarUsuario($nombre_usuario, $contrasena) {
        $query = "SELECT * FROM usuario WHERE nombre_usuario = :nombre_usuario AND contrasena = MD5(:contrasena)";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':nombre_usuario', $nombre_usuario);
        $stmt->bindParam(':contrasena', $contrasena);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function registrarUsuario($datos) {
        try {
            $this->db->beginTransaction();

            // Insertar el usuario en la tabla `usuario`
            $query = "INSERT INTO usuario (nombre, apellido, nombre_usuario, contrasena, tipo_documento, documento_identidad, email, telefono, fecha_registro)
                      VALUES (:nombre, :apellido, :nombre_usuario, MD5(:contrasena), :tipo_documento, :documento_identidad, :email, :telefono, CURDATE())";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':nombre', $datos['nombre']);
            $stmt->bindParam(':apellido', $datos['apellido']);
            $stmt->bindParam(':nombre_usuario', $datos['nombre_usuario']);
            $stmt->bindParam(':contrasena', $datos['contrasena']);
            $stmt->bindParam(':tipo_documento', $datos['tipo_documento']);
            $stmt->bindParam(':documento_identidad', $datos['documento_identidad']);
            $stmt->bindParam(':email', $datos['email']);
            $stmt->bindParam(':telefono', $datos['telefono']);
            $stmt->execute();

            // Obtener el ID del usuario recién creado
            $idusuario = $this->db->lastInsertId();

            // Asignar el rol de "cliente" al usuario
            $queryRol = "INSERT INTO usuario_has_rol (usuario_idusuario, rol_idrol, estado)
                         VALUES (:idusuario, (SELECT idrol FROM rol WHERE nombre_rol = 'cliente'), 'activo')";
            $stmtRol = $this->db->prepare($queryRol);
            $stmtRol->bindParam(':idusuario', $idusuario);
            $stmtRol->execute();

            $this->db->commit();
            return true; // Registro exitoso
        } catch (Exception $e) {
            $this->db->rollBack();
            error_log("Error al registrar usuario: " . $e->getMessage());
            return false; // Error en el registro
        }
    }
}
?>
