<?php
session_start();
require_once '../config/database.php';
require_once '../models/LugarModel.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $db = new Database();
    $conn = $db->getConnection();
    $lugarModel = new LugarModel($conn);

    $idlugar = intval($_POST['idlugar']);
    $nombre_lugar = trim($_POST['nombre_lugar']);
    $ubicacion_lugar = trim($_POST['ubicacion_lugar']);
    $descripcion_lugar = trim($_POST['descripcion_lugar']);
    $cantidad_habitaciones = intval($_POST['cantidad_habitaciones']);
    $precio_lugar = floatval($_POST['precio_lugar']);
    $tipo = trim($_POST['tipo']);
    $disponibilidad_lugar = isset($_POST['disponibilidad_lugar']) ? 1 : 0;

    // Validar campos obligatorios
    if (empty($nombre_lugar) || empty($ubicacion_lugar) || empty($descripcion_lugar)) {
        header("Location: ../views/editar_lugar.php?id=$idlugar&error=campos_obligatorios");
        exit;
    }

    // Manejar la imagen si se subió una nueva
    $imagen_lugar = null;
    if (!empty($_FILES['imagen_lugar']['tmp_name'])) {
        $imagen_lugar = file_get_contents($_FILES['imagen_lugar']['tmp_name']);
    }

    // Actualizar la información del lugar
    $datos = [
        'idlugar' => $idlugar,
        'nombre_lugar' => $nombre_lugar,
        'ubicacion_lugar' => $ubicacion_lugar,
        'descripcion_lugar' => $descripcion_lugar,
        'cantidad_habitaciones' => $cantidad_habitaciones,
        'precio_lugar' => $precio_lugar,
        'tipo' => $tipo,
        'disponibilidad_lugar' => $disponibilidad_lugar,
        'cantidad_banos' => intval($_POST['cantidad_banos']),
        'cantidad_piscinas' => intval($_POST['cantidad_piscinas']),
        'juegos_infantiles' => isset($_POST['juegos_infantiles']) ? 1 : 0,
        'zonas_verdes' => isset($_POST['zonas_verdes']) ? 1 : 0,
        'imagen_lugar' => $imagen_lugar,
    ];

    if ($lugarModel->actualizarLugar($datos)) {
        header("Location: ../views/listar_fincas.php?mensaje=actualizado");
    } else {
        header("Location: ../views/editar_lugar.php?id=$idlugar&error=fallo_actualizacion");
    }
}
?>
