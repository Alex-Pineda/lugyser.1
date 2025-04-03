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
        $query = "INSERT INTO lugar (
            nombre_lugar, imagen_lugar, tipo, ubicacion_lugar, descripcion_lugar, 
            cantidad_habitaciones, disponibilidad_lugar, precio_lugar, 
            usuario_has_rol_usuario_idusuario, usuario_has_rol_rol_idrol, 
            cantidad_banos, cantidad_piscinas, juegos_infantiles, zonas_verdes
        ) VALUES (
            :nombre_lugar, :imagen_lugar, :tipo, :ubicacion_lugar, :descripcion_lugar, 
            :cantidad_habitaciones, :disponibilidad_lugar, :precio_lugar, 
            :usuario_idusuario, :rol_idrol, 
            :cantidad_banos, :cantidad_piscinas, :juegos_infantiles, :zonas_verdes
        )";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':nombre_lugar', $datos['nombre_lugar']);
        $stmt->bindParam(':imagen_lugar', $datos['imagen_lugar']);
        $stmt->bindParam(':tipo', $datos['tipo']);
        $stmt->bindParam(':ubicacion_lugar', $datos['ubicacion_lugar']);
        $stmt->bindParam(':descripcion_lugar', $datos['descripcion_lugar']);
        $stmt->bindParam(':cantidad_habitaciones', $datos['cantidad_habitaciones']);
        $stmt->bindParam(':disponibilidad_lugar', $datos['disponibilidad_lugar']);
        $stmt->bindParam(':precio_lugar', $datos['precio_lugar']);
        $stmt->bindParam(':usuario_idusuario', $datos['usuario_idusuario']);
        $stmt->bindParam(':rol_idrol', $datos['rol_idrol']);
        $stmt->bindParam(':cantidad_banos', $datos['cantidad_banos']);
        $stmt->bindParam(':cantidad_piscinas', $datos['cantidad_piscinas']);
        $stmt->bindParam(':juegos_infantiles', $datos['juegos_infantiles']);
        $stmt->bindParam(':zonas_verdes', $datos['zonas_verdes']);

        if (!$stmt->execute()) {
            error_log("Error en insertarLugar: " . implode(", ", $stmt->errorInfo()));
            return false;
        }

        return true;
    }

    public function obtenerLugaresConUsuarioYRol($usuarioId, $rolId) {
        try {
            $query = "
                SELECT lugar.*, rol.nombre_rol AS nombre_rol
                FROM lugar
                JOIN usuario_has_rol ON lugar.usuario_has_rol_usuario_idusuario = usuario_has_rol.usuario_idusuario
                AND lugar.usuario_has_rol_rol_idrol = usuario_has_rol.rol_idrol
                JOIN rol ON usuario_has_rol.rol_idrol = rol.idrol
                WHERE usuario_has_rol.usuario_idusuario = :usuarioId
                AND usuario_has_rol.rol_idrol = :rolId
            ";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':usuarioId', $usuarioId, PDO::PARAM_INT);
            $stmt->bindParam(':rolId', $rolId, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error en obtenerLugaresConUsuarioYRol: " . $e->getMessage());
            return [];
        }
    }
}
?>
