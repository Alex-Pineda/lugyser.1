<?php
require_once __DIR__ . '/../models/Reserva.php';

class ReservaController {
    private $reserva;
    private $conn;

    public function __construct() {
        $db = new mysqli('localhost', 'root', '', 'lugyser');
        if ($db->connect_error) {
            error_log("Error de conexión: " . $db->connect_error);
            exit("No se pudo conectar a la base de datos.");
        }
        $this->reserva = new Reserva($db);
        $this->conn = new mysqli('localhost', 'root', '', 'lugyser');
        if ($this->conn->connect_error) {
            die("Conexión fallida: " . $this->conn->connect_error);
        }
    }

    public function getAllReservas() {
        return $this->reserva->getAllReservas();
    }

    public function getReservaById($idreserva) {
        return $this->reserva->getReservaById($idreserva);
    }

    public function createReserva($data) {
        // Establecer valores predeterminados si los campos no son diligenciados
        $data['fecha_reserva'] = $data['fecha_reserva'] ?? date('Y-m-d');
        $data['nombre_cliente'] = $data['nombre_cliente'] ?? 'Cliente Anónimo';
        $data['fecha_inicio'] = $data['fecha_inicio'] ?? date('Y-m-d H:i:s');
        $data['fecha_final'] = $data['fecha_final'] ?? date('Y-m-d H:i:s', strtotime('+1 day'));
        $data['cantidad_personas'] = $data['cantidad_personas'] ?? 1;
        $data['metodo_pago'] = $data['metodo_pago'] ?? 'Efectivo';
        $data['estado_reserva'] = $data['estado_reserva'] ?? 'Pendiente';

        if (!$this->validarDatos($data)) {
            return "Error: Datos insuficientes para realizar la reserva.";
        }

        return $this->reserva->createReserva(
            $data['fecha_reserva'], 
            $data['nombre_cliente'], 
            $data['fecha_inicio'], 
            $data['fecha_final'], 
            $data['cantidad_personas'], 
            $data['metodo_pago'], 
            $data['estado_reserva'],
            $data['lugar'])?
            "Reserva registrada exitosamente." : "Error al registrar la reserva.";
    }
    

    public function updateReserva($idreserva, $data) {
        return $this->reserva->updateReserva($idreserva, $data);
    }

    public function deleteReserva($idreserva) {
        return $this->reserva->deleteReserva($idreserva);
    }

    private function validarDatos($data) {
        return isset($data['fecha_reserva'], $data['nombre_cliente'], $data['fecha_inicio'], 
                      $data['fecha_final'], $data['cantidad_personas'], $data['metodo_pago'], 
                      $data['estado_reserva']) && $data['cantidad_personas'] > 0;
    }

    public function showReservas() {
        $reservas = $this->getAllReservas();
        include __DIR__ . '/../views/listar_reservas.php';
    }

    public function __destruct() {
        $this->conn->close();
    }
}
   //$lugar = $_POST['lugar'];  // O $_REQUEST['lugar']
?>
