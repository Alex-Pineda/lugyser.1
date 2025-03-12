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
</head>
<body class="bg-dark text-white">
    <div class="container mt-5">
        <div class="card bg-light text-dark">
            <div class="card-body">
                <h2 class="card-title text-center">Fincas Publicadas</h2>
                <nav class="nav justify-content-center mb-4">
                    <a class="nav-link" href="listar_fincas.php">Ver Fincas Publicadas</a>
                    <a class="nav-link" href="publicar_finca.php">Publicar Finca</a>
                    <a class="nav-link" href="reservar_finca.php">Reservar Finca</a>
                </nav>
                <div class="table-responsive">
                    <table class="table table-striped table-bordered">
                        <thead class="thead-dark">
                            <tr>
                                <th>Nombre</th>
                                <th>Ubicación</th>
                                <th>Descripción</th>
                                <th>Habitaciones</th>
                                <th>Precio</th>
                                <th>Tipo</th>
                                <th>Disponibilidad</th>
                                <th>Baños</th>
                                <th>Piscinas</th>
                                <th>Juegos Infantiles</th>
                                <th>Zonas Verdes</th>
                                <th>Imagen</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            foreach ($lugares as $lugar) {
                                echo "<tr>
                                    <td>{$lugar['nombre_lugar']}</td>
                                    <td>{$lugar['ubicacion_lugar']}</td>
                                    <td>{$lugar['descripcion_lugar']}</td>
                                    <td>{$lugar['cantidad_habitaciones']}</td>
                                    <td>{$lugar['precio_lugar']}</td>
                                    <td>{$lugar['tipo']}</td>
                                    <td>" . ($lugar['disponibilidad_lugar'] ? 'Sí' : 'No') . "</td>
                                    <td>{$lugar['cantidad_banos']}</td>
                                    <td>{$lugar['cantidad_piscinas']}</td>
                                    <td>" . ($lugar['juegos_infantiles'] ? 'Sí' : 'No') . "</td>
                                    <td>" . ($lugar['zonas_verdes'] ? 'Sí' : 'No') . "</td>
                                    <td><img src='data:image/jpeg;base64," . base64_encode($lugar['imagen_lugar']) . "' width='100' height='100'></td>
                                </tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
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