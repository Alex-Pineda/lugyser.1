<?php
include_once __DIR__ . '/../controllers/ReservaController.php';

$reservaController = new ReservaController();
$idreserva = $_GET['id'] ?? null;

if ($idreserva) {
    if ($reservaController->deleteReserva($idreserva)) {
        echo "<script>alert('Reserva eliminada exitosamente.'); window.location.href='reservar_finca.php';</script>";
    } else {
        echo "<script>alert('Error al eliminar la reserva.'); window.location.href='reservar_finca.php';</script>";
    }
} else {
    echo "<script>alert('ID de reserva no proporcionado.'); window.location.href='reservar_finca.php';</script>";
}
?>
<?php
// Incluir pie de página
include '../includes/footer.php';
?>