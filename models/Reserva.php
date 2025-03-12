<?php
class Reserva {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function createReserva($fecha_reserva, $nombre_cliente, $fecha_inicio, 
                                  $fecha_final, $cantidad_personas, $metodo_pago, $estado_reserva) {
        $stmt = $this->db->prepare("INSERT INTO reserva 
            (fecha_reserva, nombre_cliente, fecha_inicio, fecha_final, cantidad_personas, metodo_pago, estado_reserva) 
            VALUES (?, ?, ?, ?, ?, ?, ?)");

        if (!$stmt) {
            error_log("Error en la preparación de la consulta: " . $this->db->error);
            return false;
        }

        $stmt->bind_param("ssssiss", $fecha_reserva, $nombre_cliente, $fecha_inicio, 
                          $fecha_final, $cantidad_personas, $metodo_pago, $estado_reserva);

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
}
?>
