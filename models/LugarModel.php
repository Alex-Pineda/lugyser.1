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

    public function obtenerLugaresConUsuarioYRol($usuarioId, $rolNombre) {
        try {
            $query = "
                SELECT 
                    lugar.*,
                    rol.nombre_rol AS nombre_rol
                FROM 
                    lugar
                JOIN 
                    usuario_has_rol ON lugar.usuario_has_rol_usuario_idusuario = usuario_has_rol.usuario_idusuario
                    AND lugar.usuario_has_rol_rol_idrol = usuario_has_rol.rol_idrol
                JOIN 
                    rol ON usuario_has_rol.rol_idrol = rol.idrol
                WHERE 
                    usuario_has_rol.usuario_idusuario = :usuarioId
                    AND rol.nombre_rol = :rolNombre
            ";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':usuarioId', $usuarioId, PDO::PARAM_INT);
            $stmt->bindParam(':rolNombre', $rolNombre, PDO::PARAM_STR);
            $stmt->execute();
            $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Depuración: Registrar los resultados obtenidos
            error_log("Resultados obtenidos en obtenerLugaresConUsuarioYRol: " . json_encode($resultados));

            return $resultados;
        } catch (PDOException $e) {
            error_log("Error en obtenerLugaresConUsuarioYRol: " . $e->getMessage());
            return [];
        }
    }
}
?>
