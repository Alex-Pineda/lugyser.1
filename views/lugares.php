<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lugares Disponibles</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../public/assets/styles.css">
</head>
<body class="bg-dark text-white">
    <div class="container mt-5">
        <div class="card bg-light text-dark">
            <div class="card-body">
                <h2 class="card-title text-center">Lugares Disponibles</h2>
                <div class="lugares-container">
                    <?php
                    require_once '../config/database.php';
                    require_once '../models/Lugar.php';

                    $db = (new Database())->getConnection();
                    $lugar = new Lugar();
                    $lugares = $lugar->getAllLugares();

                    foreach ($lugares as $lugar) {
                        echo "<div class='lugar'>";
                        echo "<h3>" . $lugar['nombre_lugar'] . "</h3>";
                        echo "<p>" . $lugar['descripcion_lugar'] . "</p>";
                        echo "<p>Ubicación: " . $lugar['ubicacion_lugar'] . "</p>";
                        echo "<p>Precio por noche: $" . $lugar['precio_lugar'] . "</p>";
                        echo "</div>";
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/5.3.0/js/bootstrap.min.js"></script>
</body>
</html>
