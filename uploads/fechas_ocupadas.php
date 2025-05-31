<?php
require_once '../config/database.php';
header('Content-Type: application/json');

// Simulación de fechas ocupadas desde la base de datos
$fechas_ocupadas = [
    "2025-06-05",
    "2025-06-10",
    "2025-06-11",
    "2025-06-20"
];

echo json_encode($fechas_ocupadas);

