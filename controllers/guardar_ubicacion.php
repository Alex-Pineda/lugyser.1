<?php
session_start();

header('Content-Type: application/json');

// Recibimos latitud y longitud por POST
$lat = isset($_POST['lat']) ? floatval($_POST['lat']) : null;
$lon = isset($_POST['lon']) ? floatval($_POST['lon']) : null;

if ($lat !== null && $lon !== null) {
    $_SESSION['user_lat'] = $lat;
    $_SESSION['user_lon'] = $lon;

    echo json_encode(['status' => 'success']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'No se recibieron coordenadas']);
}

?>