<?php
class RolModel {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

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

    public function reemplazarRolClientePorProveedor($idusuario) {
        $query = "UPDATE usuario_has_rol 
                  SET rol_idrol = (SELECT idrol FROM rol WHERE nombre_rol = 'proveedor')
                  WHERE usuario_idusuario = :idusuario AND rol_idrol = (SELECT idrol FROM rol WHERE nombre_rol = 'cliente')";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':idusuario', $idusuario);
        $stmt->execute();
    }
}
?>
