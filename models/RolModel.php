<?php
class RolModel {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    // Obtener los roles activos de un usuario
    public function obtenerRolesPorUsuario($idusuario) {
        try {
            $query = "SELECT r.nombre_rol 
                      FROM usuario_has_rol ur
                      JOIN rol r ON ur.rol_idrol = r.idrol
                      WHERE ur.usuario_idusuario = :idusuario AND ur.estado = 'activo'";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':idusuario', $idusuario, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error en obtenerRolesPorUsuario: " . $e->getMessage());
            return [];
        }
    }

    // Verificar si un usuario existe
    public function verificarUsuarioExiste($idusuario) {
        try {
            $query = "SELECT idusuario FROM usuario WHERE idusuario = :idusuario";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':idusuario', $idusuario, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error en verificarUsuarioExiste: " . $e->getMessage());
            return false;
        }
    }

    // Verificar si un rol existe
    public function verificarRolExiste($nombreRol) {
        try {
            $query = "SELECT idrol FROM rol WHERE nombre_rol = :nombreRol";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':nombreRol', $nombreRol, PDO::PARAM_STR);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error en verificarRolExiste: " . $e->getMessage());
            return false;
        }
    }

    // Reemplazar el rol "cliente" por "proveedor" para un usuario
    public function reemplazarRolClientePorProveedor($idusuario) {
        try {
            $query = "UPDATE usuario_has_rol 
                      SET rol_idrol = (SELECT idrol FROM rol WHERE nombre_rol = 'proveedor')
                      WHERE usuario_idusuario = :idusuario AND rol_idrol = (SELECT idrol FROM rol WHERE nombre_rol = 'cliente')";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':idusuario', $idusuario, PDO::PARAM_INT);
            $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error en reemplazarRolClientePorProveedor: " . $e->getMessage());
        }
    }

    // Asignar un rol a un usuario
    public function asignarRol($idusuario, $idrol) {
        try {
            $query = "INSERT INTO usuario_has_rol (usuario_idusuario, rol_idrol, estado)
                      VALUES (:idusuario, :idrol, 'activo')
                      ON DUPLICATE KEY UPDATE estado = 'activo'";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':idusuario', $idusuario, PDO::PARAM_INT);
            $stmt->bindParam(':idrol', $idrol, PDO::PARAM_INT);
            $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error en asignarRol: " . $e->getMessage());
        }
    }
}
?>
