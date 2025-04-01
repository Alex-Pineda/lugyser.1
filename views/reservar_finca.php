<?php
session_start();

// No es necesario verificar si el usuario está registrado, ya que los clientes pueden acceder sin iniciar sesión

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
    <style>
        .card-img-top {
            width: 100%;
            height: auto;
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
        }
        .card-equal-height {
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            height: 100%;
        }
        .row-equal-height {
            display: flex;
            flex-wrap: wrap;
            align-items: stretch;
            margin-top: 3rem; /* Añadir margen superior igual a mt-5 */
        }
        .col-equal-height {
            display: flex;
            flex-direction: column;
            margin-bottom: 1rem;
        }
        .btn-dynamic {
            flex: 1;
            margin: 0 5px;
        }
        .form-label {
            text-align: center;
            display: block;
            width: 100%;
            color:rgb(2, 3, 3); /* Cambiar el color de los labels */
        }
        input {
            text-align: center;
        }
        .form-control {
            width: 100%; /* Reducir el ancho de los inputs un 30% */
            margin: 0 auto; /* Centrar los inputs */
        }
        .form-container {
            max-width: 90%; /* Disminuir el tamaño del formulario */
            margin: 0 auto;
        }
        .image-description-container {
            max-width: 90%; /* Hacer la tarjeta de imagen y descripción el doble de ancho del formulario */
            margin: 0 auto;
        }
        .image-description-container img {
            max-width: 100%;
            height: auto;
            object-fit: contain; /* Asegurar que la imagen se vea completamente */
        }
        .d-flex.gap-2 .btn-dynamic {
            margin: 0 10px; /* Separar los botones */
        }
    </style>
</head>
<body class="bg-dark text-white">

    <div id='header-container'></div> <!-- Aquí se insertará el encabezado -->
    
    <main class="container my-5">
        <h1 class="text-center mb-4">Reservar Finca</h1>
        
        <div class="row row-equal-height">
            <!-- Formulario de reserva -->
            <div class="col-md-4 col-equal-height form-container">
                <div class="card p-4 shadow card-equal-height">
                    <form action="../controllers/reservar_finca_controlador.php" method="POST" onsubmit="return validarFormulario()">
                        <h3 class="text-primary text-center">Seleccionar días</h3>
                        
                        <div class="mb-3">
                            <label for="fecha_reserva" class="form-label">Fecha de Reserva</label>
                            <input id="fecha_reserva" name="fecha_reserva" type="date" class="form-control" value="<?php echo date('Y-m-d'); ?>" readonly>
                        </div>

                        <div class="mb-3">
                            <label for="nombre_cliente" class="form-label">Nombre del Cliente</label>
                            <input id="nombre_cliente" name="nombre_cliente" type="text" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label for="fecha_inicio" class="form-label">Fecha de inicio</label>
                            <input id="fecha_inicio" name="fecha_inicio" type="datetime-local" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label for="fecha_final" class="form-label">Fecha de fin</label>
                            <input id="fecha_final" name="fecha_final" type="datetime-local" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label for="cantidad_personas" class="form-label">Cantidad de personas</label>
                            <input id="cantidad_personas" name="cantidad_personas" type="number" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label for="metodo_pago" class="form-label">Método de pago</label>
                            <select id="metodo_pago" name="metodo_pago" class="form-select form-control" required>
                                <option value="credit-card">Tarjeta de crédito</option>
                                <option value="paypal">PayPal</option>
                                <option value="bank-transfer">Transferencia bancaria</option>
                                <option>Nequi</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="estado_reserva" class="form-label">Estado de la Reserva</label>
                            <select id="estado_reserva" name="estado_reserva" class="form-select form-control" required>
                                <option value="Confirmada">Confirmada</option>
                                <option value="Pendiente">Pendiente</option>
                                <option value="Cancelada">Cancelada</option>
                            </select>
                        </div>

                        <div class="d-flex gap-2">
                            <button class="btn btn-primary btn-dynamic" type="submit">Reservar</button>
                            <a href="listar_fincas.php" class="btn btn-secondary btn-dynamic">Cancelar</a> <!-- Redirigir a listar_fincas.php -->
                        </div>
                    </form>
                </div>
            </div>

            <!-- Imagen y descripción -->
            <div class="col-md-8 col-equal-height text-center image-description-container">
                <?php if ($lugar): ?>
                    <div class="card p-3 shadow card-equal-height">
                        <img id="imagen" class="img-fluid rounded shadow card-img-top" src="data:image/jpeg;base64,<?php echo base64_encode($lugar['imagen_lugar']); ?>" alt="Finca de recreo">
                        <h2 class="mt-3 text-success fw-bold">Total: $<?php echo $lugar['precio_lugar']; ?></h2>
                        <div class="card-body">
                            <p class="text-muted">
                                <?php echo $lugar['descripcion_lugar']; ?>
                            </p>
                            <a href="#" class="btn btn-outline-primary">Ver ubicación</a>
                        </div>
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
                        echo "<div class='col-md-4 mb-4'>";
                        echo "<div class='card bg-light text-dark shadow'>";
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
    <script>
        function validarFormulario() {
            const nombreCliente = document.getElementById('nombre_cliente').value;
            const fechaInicio = document.getElementById('fecha_inicio').value;
            const fechaFinal = document.getElementById('fecha_final').value;
            const metodoPago = document.getElementById('metodo_pago').value;
            const estadoReserva = document.getElementById('estado_reserva').value;

            if (!nombreCliente || !fechaInicio || !fechaFinal || !metodoPago || !estadoReserva) {
                alert('Por favor, complete todos los campos antes de enviar el formulario.');
                return false;
            }
            return true;
        }
    </script>

</body>
</html>

<?php
// Incluir pie de página
include '../includes/footer.php';
?>
