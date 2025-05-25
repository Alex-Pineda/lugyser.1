<?php
include_once __DIR__ . '/../controllers/LugarController.php';

$lugarController = new LugarController();
$idlugar = $_GET['id'] ?? null;

if ($idlugar) {
    $lugarController->deleteLugar($idlugar);
}

header('Location: listar_fincas.php');
exit;

?>
