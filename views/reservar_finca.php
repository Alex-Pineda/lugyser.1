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

<?php
// Manejar la búsqueda y redirección antes de cualquier salida HTML
if (isset($_GET['buscar_lugar'])) {
    $buscar = trim($_GET['buscar_lugar']);
    if ($buscar !== '') {
        // Solo mostrar lugares disponibles para clientes, pero para admin/proveedor mostrar todos
        $sql = "SELECT * FROM lugar WHERE nombre_lugar LIKE ?";
        $like = "%$buscar%";
        if (!isset($_SESSION['rol']) || $_SESSION['rol'] === 'cliente') {
            $sql = "SELECT * FROM lugar WHERE disponibilidad_lugar = 1 AND nombre_lugar LIKE ?";
        }
        $stmt_buscar = $conn->prepare($sql);
        $stmt_buscar->bind_param("s", $like);
        $stmt_buscar->execute();
        $resultado_busqueda = $stmt_buscar->get_result();
        if ($resultado_busqueda->num_rows > 0) {
            $row = $resultado_busqueda->fetch_assoc();
            header("Location: reservar_finca.php?lugar_id=" . $row['idlugar']);
            exit;
        } else {
            $busqueda_error = "<div class='alert alert-warning text-center mt-3'>Lugar no disponible.</div>";
        }
    }
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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha512-o3mN5p4AwtrJdy3GQeu63Z+2frX9Z2g2YhXL0ldE2wMS5kucc+Zl5Af0GbB8J7hMfKQqz05EyL7gXoAA2PUEeQ==" crossorigin="" />
    <!-- Leaflet JS -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha512-mpwJ4npW3YiYZcLphSx+mEOoBOS1gFgGe3uAzL4tMf0CmbMqlLMj0qMLRy8sU48ZKjC0gkbxPpUIpi+u5xNlIA==" crossorigin=""></script>
    <link rel="icon" href="/lugyser/favicon-rounded.ico" type="image/x-icon">

    <style>
        .flatpickr-day.reservado {
            background-color: #f44336 !important;
            color: white !important;
            border-radius: 50%;
        }
        html, body {
            margin: 0;
            padding: 0;
            height: auto;
            overflow-x: hidden;
            padding-bottom: 80px; /* Aumenta este valor según necesites */
        }

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
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
</head>
<body class="bg-dark text-white">

    <div id='header-container'></div> <!-- Aquí se insertará el encabezado -->
   
    <!-- Barra de búsqueda de lugares -->
    <div class="container my-4 d-flex align-items-center">
        <?php
        // Mostrar mensaje de error si la búsqueda no encontró resultados
        if (isset($busqueda_error)) {
            echo $busqueda_error;
        }
        ?>
           <?php
// Determinar la URL de destino según el rol del usuario
$url_destino = '../index.php'; // Por defecto para cliente o no autenticado
if (isset($_SESSION['roles']) && is_array($_SESSION['roles'])) {
    $roles = array_column($_SESSION['roles'], 'nombre_rol');
    if (in_array('proveedor', $roles)) {
        $url_destino = 'proveedor_dashboard.php';
    } elseif (in_array('administrador', $roles)) {
        $url_destino = 'admin_dashboard.php';
    }
}
?>
<a href="<?php echo $url_destino; ?>">
    <img src="../uploads/FA.jpeg" alt="Volver" style="height:50px; border-radius: 50%; width:auto; cursor:pointer;">
</a>
        </a>
        <form method="GET" action="reservar_finca.php" class="d-flex flex-grow-1 justify-content-center">
            <input type="text" name="buscar_lugar" class="form-control w-50 mr-2" placeholder="Buscar finca por nombre..." value="<?php echo isset($_GET['buscar_lugar']) ? htmlspecialchars($_GET['buscar_lugar']) : ''; ?>">
            <button type="submit" class="btn btn-primary ml-2">Buscar</button>
        </form>
        <?php
        if (isset($_GET['buscar_lugar'])) {
            $buscar = trim($_GET['buscar_lugar']);
            if ($buscar !== '') {
                $stmt_buscar = $conn->prepare("SELECT * FROM lugar WHERE disponibilidad_lugar = 1 AND nombre_lugar LIKE ?");
                $like = "%$buscar%";
                $stmt_buscar->bind_param("s", $like);
                $stmt_buscar->execute();
                $resultado_busqueda = $stmt_buscar->get_result();
                if ($resultado_busqueda->num_rows > 0) {
                    $row = $resultado_busqueda->fetch_assoc();
                    header("Location: reservar_finca.php?lugar_id=" . $row['idlugar']);
                    exit;
                } else {
                    echo "<div class='alert alert-warning text-center mt-3'>Lugar no disponible.</div>";
                }
            }
        }
        ?>
    </div>
    <main class="container my-5">
        <h1 class="mb-4" style="text-align: left; margin-left: 57%;">Reservar Finca</h1>
        
        <div class="row row-equal-height">
            <!-- Formulario de reserva -->
            <div class="col-md-4 col-equal-height form-container">
                <div class="card p-4 shadow card-equal-height">
                    <form action="../controllers/reservar_finca_controlador.php" method="POST" onsubmit="return validarFormulario()">
                    <?php if ($lugar): ?>
                    <input type="hidden" id="lugar_reserva" name="lugar_reserva" value="<?php echo htmlspecialchars($lugar['idlugar']); ?>">
                    <?php endif; ?>
 
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
                            <input id="fecha_inicio" name="fecha_inicio" type="text" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label for="fecha_final" class="form-label">Fecha de fin</label>
                            <input id="fecha_final" name="fecha_final" type="text" class="form-control" required>
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
                            <button class="btn btn-primary btn-dynamic" src="finca.jpg" type="submit">Reservar</button>
                            <button type="button" class="btn btn-secondary btn-dynamic" onclick="window.history.back();">Cancelar</button>
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
                            <a href="ubicacionLugarView.php?idlugar=<?= $lugar['idlugar'] ?>" class="btn btn-outline-primary">Ver ubicación</a>
                            <!-- Contenedor oculto del mapa -->
                            <div id="map" style="height: 400px; display: none; margin-top: 20px;"></div>
                        </div>
                    </div>
                <?php else: ?>
                    <p class="text-muted" style="font-size: 21px; color: #ff5722;">Desliza y selecciona un lugar para ver los detalles.</p>
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

        function seleccionarLugar(nombre) {
        document.getElementById('lugar_reserva').value = nombre;
        }

        function validarFormulario() {
            const nombreCliente = document.getElementById('nombre_cliente').value;
            const fechaInicio = document.getElementById('fecha_inicio').value;
            const fechaFinal = document.getElementById('fecha_final').value;
            const metodoPago = document.getElementById('metodo_pago').value;
            const estadoReserva = document.getElementById('estado_reserva').value;
            const lugar = document.getElementById('lugar_reserva').value;

            if (!nombreCliente || !fechaInicio || !fechaFinal || !metodoPago || !estadoReserva || !lugar) {
                alert('Por favor, complete todos los campos antes de enviar el formulario.');
                return false;
            }

            return true;
        }
    </script>
    <!-- Scripts de Flatpickr -->

</script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="../uploads/mostrar_mapa.js"></script>

</body>
<script>
document.addEventListener("DOMContentLoaded", function () {
    fetch("../uploads/fechas_ocupadas.php")
        .then(res => res.json())
        .then(fechasOcupadas => {
            flatpickr("#fecha_inicio", {
                enableTime: true,
                dateFormat: "Y-m-d H:i",
                disable: fechasOcupadas,
                onDayCreate: function (dObj, dStr, fp, dayElem) {
                    const fecha = dayElem.dateObj.toISOString().split("T")[0];
                    if (fechasOcupadas.includes(fecha)) {
                        dayElem.classList.add("reservado");
                    }
                }
            });

            flatpickr("#fecha_final", {
                enableTime: true,
                dateFormat: "Y-m-d H:i",
                disable: fechasOcupadas,
                onDayCreate: function (dObj, dStr, fp, dayElem) {
                    const fecha = dayElem.dateObj.toISOString().split("T")[0];
                    if (fechasOcupadas.includes(fecha)) {
                        dayElem.classList.add("reservado");
                    }
                }
            });
        })
        .catch(error => console.error("Error cargando fechas ocupadas:", error));
});
</script>

</html>

<?php
// Incluir pie de página
include '../includes/footer.php';
?>
