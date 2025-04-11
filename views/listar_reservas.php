<?php
// Incluir encabezado y controlador de reservas
include_once __DIR__ . '/../includes/header.php';
include_once __DIR__ . '/../controllers/ReservaController.php';

if (class_exists('ReservaController')) {
    $reservaController = new ReservaController();
    $reservas = $reservaController->getAllReservas();
} else {
    die('Error: No se pudo encontrar la clase ReservaController.');
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reservas Realizadas</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        html, body {
    margin: 0;
    padding: 0;
    height: auto;
    overflow-x: hidden;
    padding-bottom: 100px; /* Aumenta este valor según necesites */
}

        .table-responsive {
            overflow-x: auto;
        }
    </style>
</head>
<body class="bg-light text-dark">
    <div class="container mt-5">
        <h1 class="text-center">Reservas Realizadas</h1>
        <div class="table-responsive">
            <?php if (!empty($reservas)): ?>
                <table class="table table-striped">
                    <thead class="thead-dark">
                        <tr>
                            <th>ID</th>
                            <th>Nombre Cliente</th>
                            <th>Fecha Inicio</th>
                            <th>Fecha Final</th>
                            <th>Cantidad Personas</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($reservas as $reserva): ?>
                            <tr>
                                <td><?php echo $reserva['idreserva']; ?></td>
                                <td><?php echo $reserva['nombre_cliente']; ?></td>
                                <td><?php echo $reserva['fecha_inicio']; ?></td>
                                <td><?php echo $reserva['fecha_final']; ?></td>
                                <td><?php echo $reserva['cantidad_personas']; ?></td>
                                <td><?php echo $reserva['estado_reserva']; ?></td>
                                <td>
                                    <a href="editar_reserva.php?id=<?php echo $reserva['idreserva']; ?>" class="btn btn-warning btn-sm">Editar</a>
                                    <a href="eliminar_reserva.php?id=<?php echo $reserva['idreserva']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('¿Está seguro de que desea eliminar esta reserva?');">Eliminar</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p class="text-center">No hay reservas realizadas.</p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
<?php
// Incluir pie de página
include_once __DIR__ . '/../includes/footer.php';
?>