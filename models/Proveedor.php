<?php
class Proveedor {
    private $conn;
    private $table_name = "proveedor";

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getAllProveedores() {
        $query = "SELECT * FROM " . $this->table_name;
        $result = $this->conn->query($query);
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function getProveedorById($idproveedor) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE idproveedor = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $idproveedor);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function createProveedor($razon_social, $tipo_oferta, $descripcion_oferta, $usuario_idusuario, $rol_idrol) {
        $query = "INSERT INTO " . $this->table_name . " (razon_social, tipo_oferta, descripcion_oferta, usuario_has_rol_usuario_idusuario, usuario_has_rol_rol_idrol) VALUES (?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("sssii", $razon_social, $tipo_oferta, $descripcion_oferta, $usuario_idusuario, $rol_idrol);
        return $stmt->execute();
    }
}
?>
