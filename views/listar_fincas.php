<?php
// Incluir encabezado y controlador de lugares
include '../includes/header.php';
include '../controllers/LugarController.php';

if (class_exists('LugarController')) {
    $lugarController = new LugarController();
    $lugares = $lugarController->getAllLugares();
} else {
    die('Error: No se pudo encontrar la clase LugarController.');
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fincas Publicadas</title>
    <link rel="stylesheet" href="../css/Estilos/listar_fincas.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        .card-img-top {
            width: 100%;
            height: 200px;
            object-fit: cover;
        }
        .card {
            height: 100%;
            display: flex;
            flex-direction: column;
        }
        .card-body {
            display: flex;
            flex-direction: column;
            flex-grow: 1;
        }
        .card-title {
            font-size: 1.25rem;
            font-weight: bold;
        }
        .card-text {
            flex-grow: 1;
        }
        .btn-group {
            display: flex;
            justify-content: space-between;
            gap: 10px; /* Generar un pequeño espacio entre los botones */
        }
        .row-equal-height {
            display: flex;
            flex-wrap: wrap;
        }
        .col-equal-height {
            display: flex;
            flex-direction: column;
            margin-bottom: 1rem;
        }
        h1.text-center {
            height: 80px; /* Estilo solicitado */
            color: aliceblue; /* Estilo solicitado */
        }
    </style>
</head>
<body class="bg-dark text-white">
    <div class="container mt-5">
        <h1 class="text-center">Fincas Publicadas</h1>
        <div class="row row-equal-height">
            <?php if (!empty($lugares)): ?>
                <?php foreach ($lugares as $lugar): ?>
                    <div class="col-md-4 col-equal-height">
                        <div class="card bg-light text-dark shadow">
                            <img src="data:image/jpeg;base64,<?php echo base64_encode($lugar['imagen_lugar']); ?>" class="card-img-top" alt="<?php echo $lugar['nombre_lugar']; ?>">
                            <div class="card-body">
                                <h5 class="card-title"><?php echo $lugar['nombre_lugar']; ?></h5>
                                <p class="card-text"><?php echo $lugar['descripcion_lugar']; ?></p>
                                <div class="btn-group">
                                    <a href="editar_lugar.php?id=<?php echo $lugar['idlugar']; ?>" class="btn btn-warning btn-sm">Editar</a>
                                    <a href="eliminar_lugar.php?id=<?php echo $lugar['idlugar']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('¿Está seguro de que desea eliminar este lugar?');">Eliminar</a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="text-center">No hay fincas publicadas.</p>
            <?php endif; ?>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>

<?php
// Incluir pie de página
include '../includes/footer.php';
?>