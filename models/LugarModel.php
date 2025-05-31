<?php
class LugarModel {
    private $conn;

    public function __construct($dbConnection) {
        $this->conn = $dbConnection;
    }

    public function obtenerLugaresPorUsuario($usuarioId) {
        try {
            $query = "SELECT * FROM lugar WHERE usuario_has_rol_usuario_idusuario = :usuarioId";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':usuarioId', $usuarioId, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error en obtenerLugaresPorUsuario: " . $e->getMessage());
            return [];
        }
    }

    public function obtenerTodosLosLugares() {
        try {
            $query = "SELECT * FROM lugar";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error en obtenerTodosLosLugares: " . $e->getMessage());
            return [];
        }
    }

    public function insertarLugar($datos) {
        try {
            $query = "INSERT INTO lugar (nombre_lugar, ubicacion_lugar, descripcion_lugar, cantidad_habitaciones, precio_lugar, tipo, disponibilidad_lugar, cantidad_banos, cantidad_piscinas, juegos_infantiles, zonas_verdes, imagen_lugar, usuario_has_rol_usuario_idusuario, usuario_has_rol_rol_idrol) VALUES (:nombre, :ubicacion, :descripcion, :habitaciones, :precio, :tipo, :disponibilidad, :banos, :piscinas, :juegos, :zonas, :imagen, :usuarioId, :rolId)";
            $stmt = $this->conn->prepare($query);
            return $stmt->execute($datos);
        } catch (PDOException $e) {
            error_log("Error en insertarLugar: " . $e->getMessage());
            return false;
        }
    }
    public function obtenerLugaresConUsuarioYRol($idusuario, $nombreRol) {
    try {
        $query = "
            SELECT l.*
            FROM lugar l
            INNER JOIN usuario_has_rol uhr 
                ON l.usuario_has_rol_usuario_idusuario = uhr.usuario_idusuario 
                AND l.usuario_has_rol_rol_idrol = uhr.rol_idrol
            INNER JOIN rol r 
                ON uhr.rol_idrol = r.idrol
            WHERE uhr.usuario_idusuario = :idusuario
              AND r.nombre_rol = :nombreRol
        ";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':idusuario', $idusuario, PDO::PARAM_INT);
        $stmt->bindParam(':nombreRol', $nombreRol, PDO::PARAM_STR);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Error al obtener lugares del proveedor: " . $e->getMessage());
        return [];
    }
}
    
    public function eliminarLugar($idlugar) {
        try {
            $query = "DELETE FROM lugar WHERE idlugar = :idlugar";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':idlugar', $idlugar, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error en eliminarLugar: " . $e->getMessage());
            return false;
        }
    }

    public function obtenerLugarPorId($idlugar) {
        try {
            $query = "SELECT * FROM lugar WHERE idlugar = :idlugar";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':idlugar', $idlugar, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error en obtenerLugarPorId: " . $e->getMessage());
            return false;
        }
    }

    public function actualizarLugar($datos) {
        try {
            $query = "UPDATE lugar SET 
                        nombre_lugar = :nombre_lugar,
                        ubicacion_lugar = :ubicacion_lugar,
                        descripcion_lugar = :descripcion_lugar,
                        cantidad_habitaciones = :cantidad_habitaciones,
                        precio_lugar = :precio_lugar,
                        tipo = :tipo,
                        disponibilidad_lugar = :disponibilidad_lugar";

            if (!empty($datos['imagen_lugar'])) {
                $query .= ", imagen_lugar = :imagen_lugar";
            }

            $query .= " WHERE idlugar = :idlugar";

            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':nombre_lugar', $datos['nombre_lugar']);
            $stmt->bindParam(':ubicacion_lugar', $datos['ubicacion_lugar']);
            $stmt->bindParam(':descripcion_lugar', $datos['descripcion_lugar']);
            $stmt->bindParam(':cantidad_habitaciones', $datos['cantidad_habitaciones']);
            $stmt->bindParam(':precio_lugar', $datos['precio_lugar']);
            $stmt->bindParam(':tipo', $datos['tipo']);
            $stmt->bindParam(':disponibilidad_lugar', $datos['disponibilidad_lugar']);
            $stmt->bindParam(':idlugar', $datos['idlugar'], PDO::PARAM_INT);

            if (!empty($datos['imagen_lugar'])) {
                $stmt->bindParam(':imagen_lugar', $datos['imagen_lugar'], PDO::PARAM_LOB);
            }

            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error en actualizarLugar: " . $e->getMessage());
            return false;
        }
    }

}
?>
