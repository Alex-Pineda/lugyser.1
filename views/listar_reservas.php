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
        width: 100vw;
        overflow-x: hidden;
        background-color: #f9f9f9;
    }

    .table-wrapper {
        width: 100%;
        overflow-x: auto;
        padding: 0;
        margin: 0;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        font-size: 18px;
        table-layout: auto;
    }

    th, td {
        padding: 12px 16px;
        border: 1px solid #ccc;
        text-align: left;
    }

    th {
        background-color: #eee;
    }

    .form-inline .form-control {
        width: 400px;
        margin-top: 8px;
    }

    .form-inline .btn {
        margin-left: -10px;
        margin-top: 8px;
    }
</style>

<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>

<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>


<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css">
<script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>

<!-- Archivos para exportar -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>

</head>

<body class="bg-light text-dark">
    <div class="container-fluid mt-5 px-0">
        <div class="d-flex justify-content-between align-items-center mb-4 px-3">
            <h1 class="mb-0" style="max-width: 95%; margin: 0 auto;">Reservas Realizadas</h1>
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
            <div class="table-responsive" style="max-width: 95%; margin: 0 auto;">
        <?php if (!empty($reservas)): ?>
            <table id="tablaReservas" class="table table-striped">
                <thead class="thead-dark">

                        <tr>
                            <th>ID</th>
                            <th>Nombre Cliente</th>
                            <th>Nombre del Lugar</th>
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
                                <td><?php echo $reserva['nombre_lugar']; ?></td>
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
    <script>
$(document).ready(function() {
    $('#tablaReservas').DataTable({
        dom: 'Bfrtip',
        buttons: ['copy', 'excel', 'pdf', 'print'],
        language: {
            url: "//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json"
        }
    });
});
</script>

</body>
</html>
<?php
// Incluir pie de página
include_once __DIR__ . '/../includes/footer.php';
?>
