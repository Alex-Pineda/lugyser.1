<?php
session_start();
require_once '../config/database.php'; // Archivo que configura la conexión a la base de datos

$db = new Database();
$conn = $db->getConnection();

// Obtener la lista de lugares desde la tabla lugar
$query = "SELECT * FROM lugar";
$stmt = $conn->prepare($query);
$stmt->execute();
$lugares = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Lugares - FincAntioquia</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="icon" href="/lugyser/favicon-rounded.ico" type="image/x-icon">

    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #f8f9fa;
            color: #333;
        }
        html, body {
            margin: 0;
            padding: 0;
            height: auto;
            overflow-x: hidden;
            padding-bottom: 80px; /* Aumenta este valor según necesites */
        }

        .container {
            margin-top: 2rem;
        }
        .card {
            margin-bottom: 1rem;
        }
    </style>
    <script>
        function mostrarMensaje() {
            alert('Debes registrarte para poder reservar.');
        }
    </script>
</head>
<body>
    <div class="container">
        <h1 class="text-center">Lista de Lugares</h1>
        <div class="row">
            <?php foreach ($lugares as $lugar): ?>
                <div class="col-md-4">
                    <div class="card">
                        <img src="data:image/jpeg;base64,<?php echo base64_encode($lugar['imagen']); ?>" class="card-img-top" alt="Imagen del lugar">
                        <div class="card-body">
                            <h5 class="card-title"><?php echo htmlspecialchars($lugar['nombre']); ?></h5>
                            <p class="card-text"><?php echo htmlspecialchars($lugar['descripcion']); ?></p>
                            <?php if (isset($_SESSION['usuario'])): ?>
                                <a href="reservar.php?id=<?php echo $lugar['id']; ?>" class="btn btn-primary">Reservar</a>
                            <?php else: ?>
                                <button onclick="mostrarMensaje()" class="btn btn-secondary">Reservar</button>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</body>
</html>
<?php
// Incluir pie de página
include '../includes/footer.php';
?>
