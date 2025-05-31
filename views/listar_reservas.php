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
    <link rel="icon" href="/lugyser/favicon-rounded.ico" type="image/x-icon">

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
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="mb-0">Reservas Realizadas</h1>
            <form method="get" class="form-inline">
            <div class="form-group mx-sm-3 mb-2">
                <input type="text" name="buscar_nombre" class="form-control" placeholder="Buscar por Nombre de Cliente" style="width: 400px;" value="<?php echo isset($_GET['buscar_nombre']) ? htmlspecialchars($_GET['buscar_nombre']) : ''; ?>">
            </div>
            <button type="submit" class="btn btn-primary mb-2">Buscar</button>
            </form>
        </div>
        <?php
        // Filtrar reservas por nombre si se envió el formulario
        if (isset($_GET['buscar_nombre']) && $_GET['buscar_nombre'] !== '') {
            $buscar = strtolower(trim($_GET['buscar_nombre']));
            $reservas = array_filter($reservas, function($reserva) use ($buscar) {
            return strpos(strtolower($reserva['nombre_cliente']), $buscar) !== false;
            });
        }
        ?>
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
