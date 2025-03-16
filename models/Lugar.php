<?php
class Lugar {
    private $db;

    public function __construct() {
        $this->db = new mysqli('localhost', 'root', '', 'lugyser');
        if ($this->db->connect_error) {
            die("Connection failed: " . $this->db->connect_error);
        }
    }

    public function getAllLugares() {
        $result = $this->db->query("SELECT * FROM lugar");
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function getLugarById($idlugar) {
        $stmt = $this->db->prepare("SELECT * FROM lugar WHERE idlugar = ?");
        $stmt->bind_param("i", $idlugar);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function createLugar($nombre, $ubicacion, $descripcion, $cantidad_habitaciones, $precio, $imagen, $usuario_id, $rol_id, $tipo, $disponibilidad, $cantidad_banos, $cantidad_piscinas, $juegos_infantiles, $zonas_verdes) {
        $stmt = $this->db->prepare("INSERT INTO lugar (nombre_lugar, ubicacion_lugar, descripcion_lugar, cantidad_habitaciones, precio_lugar, imagen_lugar, usuario_has_rol_usuario_idusuario, usuario_has_rol_rol_idrol, tipo, disponibilidad_lugar, cantidad_banos, cantidad_piscinas, juegos_infantiles, zonas_verdes) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssidsiisiisii", $nombre, $ubicacion, $descripcion, $cantidad_habitaciones, $precio, $imagen, $usuario_id, $rol_id, $tipo, $disponibilidad, $cantidad_banos, $cantidad_piscinas, $juegos_infantiles, $zonas_verdes);
        return $stmt->execute();
    }

    public function updateLugar($idlugar, $data) {
        $stmt = $this->db->prepare("UPDATE lugar SET nombre_lugar = ?, ubicacion_lugar = ?, descripcion_lugar = ?, cantidad_habitaciones = ?, precio_lugar = ?, imagen_lugar = ?, usuario_has_rol_usuario_idusuario = ?, usuario_has_rol_rol_idrol = ?, tipo = ?, disponibilidad_lugar = ?, cantidad_banos = ?, cantidad_piscinas = ?, juegos_infantiles = ?, zonas_verdes = ? WHERE idlugar = ?");
        $stmt->bind_param("sssidsiisiisiii", $data['nombre_lugar'], $data['ubicacion_lugar'], $data['descripcion_lugar'], $data['cantidad_habitaciones'], $data['precio_lugar'], $data['imagen_lugar'], $data['usuario_idusuario'], $data['rol_idrol'], $data['tipo'], $data['disponibilidad_lugar'], $data['cantidad_banos'], $data['cantidad_piscinas'], $data['juegos_infantiles'], $data['zonas_verdes'], $idlugar);
        return $stmt->execute();
    }

    public function deleteLugar($idlugar) {
        $stmt = $this->db->prepare("DELETE FROM lugar WHERE idlugar = ?");
        $stmt->bind_param("i", $idlugar);
        return $stmt->execute();
    }
}
?>
