<?php
require_once '../config/database.php';

$lugar_id = $_GET['lugar_id'] ?? 0;

$sql = "SELECT fecha_inicio, fecha_final FROM reserva WHERE lugar_reserva = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $lugar_id);
$stmt->execute();
$result = $stmt->get_result();

$fechas = [];

while ($row = $result->fetch_assoc()) {
    $inicio = new DateTime($row['fecha_inicio']);
    $fin = new DateTime($row['fecha_final']);
    while ($inicio <= $fin) {
        $fechas[] = $inicio->format('Y-m-d');
        $inicio->modify('+1 day');
    }
}

echo json_encode(array_unique($fechas));
