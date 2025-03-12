<?php
// Incluir encabezado y conexión a la base de datos si es necesario
include '../config/database.php';

// Conectar a la base de datos
$conn = new mysqli('localhost', 'root', '', 'lugyser');

// Verificar la conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

$lugar_id = $_GET['lugar_id'] ?? null;
$lugar = null;

if ($lugar_id) {
    $stmt = $conn->prepare("SELECT * FROM lugar WHERE idlugar = ?");
    $stmt->bind_param("i", $lugar_id);
    $stmt->execute();
    $lugar = $stmt->get_result()->fetch_assoc();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reservar Finca</title>
    <link rel="stylesheet" href="../css/styles.css">
    <link rel="stylesheet" href="../css/styles_reserva.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/Estilos/reservar_finca.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body class="bg-dark text-white">

    <div id='header-container'></div> <!-- Aquí se insertará el encabezado -->
    
    <main class="container my-5">
        <h1 class="text-center mb-4">Reservar Finca</h1>
        
        <div class="row">
            <!-- Formulario de reserva -->
            <div class="col-md-6">
                <div class="card p-4 shadow">
                    <form action="../controllers/reservar_finca_controlador.php" method="POST">
                        <h3 class="text-primary">Seleccionar días</h3>
                        
                        <div class="mb-3">
                            <label for="fecha_reserva" class="form-label">Fecha de Reserva <span class="text-danger">*</span>:</label>
                            <input id="fecha_reserva" name="fecha_reserva" type="date" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label for="nombre_cliente" class="form-label">Nombre del Cliente <span class="text-danger">*</span>:</label>
                            <input id="nombre_cliente" name="nombre_cliente" type="text" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label for="fecha_inicio" class="form-label">Fecha de inicio <span class="text-danger">*</span>:</label>
                            <input id="fecha_inicio" name="fecha_inicio" type="datetime-local" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label for="fecha_final" class="form-label">Fecha de fin <span class="text-danger">*</span>:</label>
                            <input id="fecha_final" name="fecha_final" type="datetime-local" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label for="cantidad_personas" class="form-label">Número de personas <span class="text-danger">*</span>:</label>
                            <input id="cantidad_personas" name="cantidad_personas" type="number" class="form-control" placeholder="Escribe el número de personas" required>
                        </div>

                        <div class="mb-3">
                            <label for="metodo_pago" class="form-label">Método de pago <span class="text-danger">*</span>:</label>
                            <select id="metodo_pago" name="metodo_pago" class="form-select" required>
                                <option value="credit-card">Tarjeta de crédito</option>
                                <option value="paypal">PayPal</option>
                                <option value="bank-transfer">Transferencia bancaria</option>
                                <option>Nequi</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="estado_reserva" class="form-label">Estado de la Reserva <span class="text-danger">*</span>:</label>
                            <select id="estado_reserva" name="estado_reserva" class="form-select" required>
                                <option value="Confirmada">Confirmada</option>
                                <option value="Pendiente">Pendiente</option>
                                <option value="Cancelada">Cancelada</option>
                            </select>
                        </div>

                        <div class="d-flex gap-2">
                            <button class="btn btn-primary w-50" type="submit">Reservar</button>
                            <button class="btn btn-secondary w-50" type="reset">Cancelar</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Imagen y descripción -->
            <div class="col-md-6 text-center">
                <?php if ($lugar): ?>
                    <img id="imagen" class="img-fluid rounded shadow" src="data:image/jpeg;base64,<?php echo base64_encode($lugar['imagen_lugar']); ?>" alt="Finca de recreo">
                    <h2 class="mt-3 text-success fw-bold">Total: $<?php echo $lugar['precio_lugar']; ?></h2>

                    <div class="card p-3 mt-3 shadow">
                        <p class="text-muted">
                            <?php echo $lugar['descripcion_lugar']; ?>
                        </p>
                        <a href="#" class="btn btn-outline-primary">Ver ubicación</a>
                    </div>
                <?php else: ?>
                    <p class="text-muted">Selecciona un lugar para ver los detalles.</p>
                <?php endif; ?>
            </div>
        </div>

        <!-- Lista de fincas disponibles -->
        <div class="mt-5">
            <h2 class="text-center text-dark">Fincas Disponibles</h2>
            <div class="row">
                <?php
                $query = "SELECT * FROM lugar WHERE disponibilidad_lugar = 1";
                $result = $conn->query($query);

                if ($result === false) {
                    echo "<p class='text-danger'>Error en la consulta: " . $conn->error . "</p>";
                } elseif ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<div class='col-md-4'>";
                        echo "<div class='card mb-4 shadow'>";
                        echo "<a href='reservar_finca.php?lugar_id=" . $row['idlugar'] . "'>";
                        echo "<img src='data:image/jpeg;base64," . base64_encode($row['imagen_lugar']) . "' class='card-img-top' alt='" . $row['nombre_lugar'] . "'>";
                        echo "</a>";
                        echo "<div class='card-body'>";
                        echo "<h5 class='card-title'>" . $row['nombre_lugar'] . "</h5>";
                        echo "<p class='card-text'><strong>Tipo:</strong> " . $row['tipo'] . "</p>";
                        echo "<p class='card-text'><strong>Ubicación:</strong> " . $row['ubicacion_lugar'] . "</p>";
                        echo "<p class='card-text'><strong>Descripción:</strong> " . $row['descripcion_lugar'] . "</p>";
                        echo "<p class='card-text'><strong>Habitaciones:</strong> " . $row['cantidad_habitaciones'] . "</p>";
                        echo "<p class='card-text text-success fw-bold'>Precio: $" . $row['precio_lugar'] . "</p>";
                        echo "</div></div></div>";
                    }
                } else {
                    echo "<p class='text-center text-muted'>No hay fincas disponibles en este momento.</p>";
                }
                ?>
            </div>
        </div>

        <!-- Ícono de WhatsApp flotante -->
        <a href="https://wa.me/tuNumeroDeTelefono" class="whatsapp-icon btn btn-success btn-lg rounded-circle position-fixed bottom-0 end-0 m-4" target="_blank">
            <i class="fab fa-whatsapp"></i>
        </a>
    </main>

    <div id='footer-container'></div> <!-- Aquí se insertará el pie de página -->

    <!-- Bootstrap JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../js/main.js" defer></script>
    <script src="../js/imagen_aleatorio.js" defer></script>

</body>
</html>

<?php
// Incluir pie de página
include '../includes/footer.php';
?>
