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

public function verificarUsuarioExiste($idUsuario) {
    $sql = "SELECT * FROM usuario WHERE idusuario = :id";
    $stmt = $this->db->prepare($sql);
    $stmt->execute(['id' => $idUsuario]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

public function verificarRolExiste($nombreRol) {
    $sql = "SELECT * FROM rol WHERE nombre_rol = :nombre";
    $stmt = $this->db->prepare($sql);
    $stmt->execute(['nombre' => $nombreRol]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
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
    public function asignarRolUnico($idUsuario, $idRol) {
        try {
            // Desactivar todos los roles anteriores del usuario (no eliminar, solo inactivar)
            $sqlInactivar = "UPDATE usuario_has_rol SET estado = 'inactivo' WHERE usuario_idusuario = :usuario_id";
            $stmtInactivar = $this->db->prepare($sqlInactivar);
            $stmtInactivar->execute(['usuario_id' => $idUsuario]);

            // Verificar si ya existe esa combinación de usuario y rol
            $sqlCheck = "SELECT * FROM usuario_has_rol WHERE usuario_idusuario = :usuario_id AND rol_idrol = :rol_id";
            $stmtCheck = $this->db->prepare($sqlCheck);
            $stmtCheck->execute([
                'usuario_id' => $idUsuario,
                'rol_id' => $idRol
            ]);

            if ($stmtCheck->rowCount() > 0) {
                // Si ya existe, actualizar el estado a activo
                $sqlActualizar = "UPDATE usuario_has_rol SET estado = 'activo' 
                                WHERE usuario_idusuario = :usuario_id AND rol_idrol = :rol_id";
                $stmtActualizar = $this->db->prepare($sqlActualizar);
                $stmtActualizar->execute([
                    'usuario_id' => $idUsuario,
                    'rol_id' => $idRol
                ]);
            } else {
                // Si no existe, insertar el nuevo rol
                $sqlInsertar = "INSERT INTO usuario_has_rol (usuario_idusuario, rol_idrol, estado)
                                VALUES (:usuario_id, :rol_id, 'activo')";
                $stmtInsertar = $this->db->prepare($sqlInsertar);
                $stmtInsertar->execute([
                    'usuario_id' => $idUsuario,
                    'rol_id' => $idRol
                ]);
            }
        } catch (PDOException $e) {
            error_log("Error en asignarRolUnico: " . $e->getMessage());
        }
    }
}
?>
