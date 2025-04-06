<?php
session_start();
require_once '../config/database.php';
require_once '../models/LugarModel.php';

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['id'])) {
    $db = new Database();
    $conn = $db->getConnection();
    $lugarModel = new LugarModel($conn);

    $idlugar = intval($_GET['id']);

    if ($lugarModel->eliminarLugar($idlugar)) {
        header("Location: ../views/proveedor_dashboard.php?mensaje=eliminado");
    } else {
        header("Location: ../views/proveedor_dashboard.php?error=fallo_eliminacion");
    }
}
?>
