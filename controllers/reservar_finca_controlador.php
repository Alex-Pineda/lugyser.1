<?php
require_once '../models/Reserva.php';

class ReservarFincaController {
    private $reserva;

    public function __construct() {
        $db = new mysqli('localhost', 'root', '', 'lugyser');
        if ($db->connect_error) {
            error_log("Error de conexión: " . $db->connect_error);
            exit("No se pudo conectar a la base de datos.");
        }
        $this->reserva = new Reserva($db);
    }

    public function createReserva($data) {
        // Validar que todos los datos necesarios estén presentes
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

    private function validarDatos($data) {
        return isset($data['fecha_reserva'], $data['nombre_cliente'], $data['fecha_inicio'], 
                      $data['fecha_final'], $data['cantidad_personas'], 
                      $data['metodo_pago'], $data['estado_reserva']) 
               && $data['cantidad_personas'] > 0;
    }
}

// Verificar si la solicitud es POST
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    exit("Acceso no permitido.");
}

// Obtener los datos del formulario de manera segura
$data = [
    'fecha_reserva' => $_POST['fecha_reserva'] ?? date("Y-m-d"),
    'nombre_cliente' => $_POST['nombre_cliente'] ?? 'Desconocido',
    'fecha_inicio' => $_POST['fecha_inicio'] ?? null,
    'fecha_final' => $_POST['fecha_final'] ?? null,
    'cantidad_personas' => $_POST['cantidad_personas'] ?? 0,
    'metodo_pago' => $_POST['metodo_pago'] ?? 'Efectivo',
    'estado_reserva' => $_POST['estado_reserva'] ?? 'Pendiente'
];

$reservarFincaController = new ReservarFincaController();
$resultado = $reservarFincaController->createReserva($data);
echo $resultado;
$lugar = $_POST['lugar'];  // O $_REQUEST['lugar']

?>
