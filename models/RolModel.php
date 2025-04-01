<?php
class RolModel {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function obtenerRolesPorUsuario($idusuario) {
        $query = "SELECT r.nombre_rol FROM usuario_has_rol uhr
                  JOIN rol r ON uhr.rol_idrol = r.idrol
                  WHERE uhr.usuario_idusuario = :idusuario AND uhr.estado = 'activo'";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':idusuario', $idusuario);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC); // Verifica que devuelva correctamente los roles
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
