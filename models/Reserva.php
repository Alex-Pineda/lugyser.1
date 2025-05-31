<?php
class Reserva {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function getFechasReservadas($lugar_id) {
    $stmt = $this->db->prepare("SELECT fecha_inicio, fecha_final FROM reserva WHERE lugar_reserva = ?");
    $stmt->bind_param("i", $lugar_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $fechas = [];

    while ($row = $result->fetch_assoc()) {
        $inicio = new DateTime($row['fecha_inicio']);
        $fin = new DateTime($row['fecha_final']);
        while ($inicio <= $fin) {
            $fechas[] = $inicio->format("Y-m-d");
            $inicio->modify("+1 day");
        }
    }

    return array_values(array_unique($fechas)); // Evitar duplicados
}


    public function createReserva($fecha_reserva, $nombre_cliente, $fecha_inicio, 
                                $fecha_final, $cantidad_personas, $metodo_pago, 
                                $estado_reserva, $lugar_reserva) {
        // Validar si ya existe una reserva en ese rango para ese lugar
        $stmtVerificar = $this->db->prepare("
            SELECT COUNT(*) 
            FROM reserva 
            WHERE lugar_reserva = ? 
            AND (
                (fecha_inicio < ? AND fecha_final > ?)  -- Cruce total o parcial
            )
        ");

        if (!$stmtVerificar) {
            error_log("Error al preparar verificación: " . $this->db->error);
            return false;
        }

        $stmtVerificar->bind_param("iss", $lugar_reserva, $fecha_final, $fecha_inicio);
        $stmtVerificar->execute();
        $existe = 0;
        $stmtVerificar->bind_result($existe);
        $stmtVerificar->fetch();
        $stmtVerificar->close();

        if ($existe > 0) {
            return "Error: El lugar ya está reservado en ese rango de fechas.";
        }

        // Si no hay conflicto, insertar la reserva
        $stmt = $this->db->prepare("INSERT INTO reserva 
            (fecha_reserva, nombre_cliente, fecha_inicio, fecha_final, 
            cantidad_personas, metodo_pago, estado_reserva, lugar_reserva) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)");

        if (!$stmt) {
            error_log("Error en la preparación de la consulta: " . $this->db->error);
            return false;
        }

        $stmt->bind_param("ssssissi", $fecha_reserva, $nombre_cliente, $fecha_inicio, 
                        $fecha_final, $cantidad_personas, $metodo_pago, 
                        $estado_reserva, $lugar_reserva);

        $resultado = $stmt->execute();
        $stmt->close();

        return $resultado ? "Reserva registrada exitosamente." : "Error al registrar la reserva.";
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
        $stmt = $this->db->prepare("UPDATE reserva SET fecha_reserva = ?, nombre_cliente = ?, fecha_inicio = ?, fecha_final = ?, cantidad_personas = ?, metodo_pago = ?, estado_reserva = ? WHERE idreserva = ?");
        $stmt->bind_param("ssssissi", $data['fecha_reserva'], $data['nombre_cliente'], $data['fecha_inicio'], $data['fecha_final'], $data['cantidad_personas'], $data['metodo_pago'], $data['estado_reserva'], $idreserva);
        return $stmt->execute();
    }

    public function deleteReserva($idreserva) {
        $stmt = $this->db->prepare("DELETE FROM reserva WHERE idreserva = ?");
        $stmt->bind_param("i", $idreserva);
        return $stmt->execute();
    }
}
?>

