<?php
class Reserva {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function createReserva($fecha_reserva, $nombre_cliente, $fecha_inicio, 
                                  $fecha_final, $cantidad_personas, $metodo_pago, $estado_reserva, $lugar) {
        $stmt = $this->db->prepare("INSERT INTO reserva 
            (fecha_reserva, nombre_cliente, fecha_inicio, fecha_final, cantidad_personas, metodo_pago, estado_reserva, lugar) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)");

        if (!$stmt) {
            error_log("Error en la preparación de la consulta: " . $this->db->error);
            return false;
        }

        $stmt->bind_param("ssssisss", $fecha_reserva, $nombre_cliente, $fecha_inicio, 
                          $fecha_final, $cantidad_personas, $metodo_pago, $estado_reserva, $lugar);

        $resultado = $stmt->execute();
        $stmt->close();

        return $resultado;
    }

    public function getAllReservas() {
        $result = $this->db->query("SELECT * FROM reserva");
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function getReservaById($idreserva) {
        $stmt = $this->db->prepare("SELECT * FROM reserva WHERE idreserva = ?");
        $stmt->bind_param("i", $idreserva);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function updateReserva($idreserva, $data) {
        $stmt = $this->db->prepare("UPDATE reserva SET fecha_reserva = ?, nombre_cliente = ?, fecha_inicio = ?, fecha_final = ?, cantidad_personas = ?, metodo_pago = ?, estado_reserva = ?, lugar = ? WHERE idreserva = ?");
        $stmt->bind_param("ssssisssi", $data['fecha_reserva'], $data['nombre_cliente'], $data['fecha_inicio'], $data['fecha_final'], $data['cantidad_personas'], $data['metodo_pago'], $data['estado_reserva'], $data['lugar'], $idreserva);
        return $stmt->execute();
    }

    public function deleteReserva($idreserva) {
        $stmt = $this->db->prepare("DELETE FROM reserva WHERE idreserva = ?");
        $stmt->bind_param("i", $idreserva);
        return $stmt->execute();
    }
}
?>
