<?php

// Conectar a la base de datos
$conn = new mysqli('localhost', 'root', '', 'lugyser');

// Verificar la conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

$id = $_GET['id'] ?? null;
$reserva = null;

if ($id) {
    $stmt = $conn->prepare("SELECT * FROM reserva WHERE idreserva = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $reserva = $stmt->get_result()->fetch_assoc();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre_cliente = $_POST['nombre_cliente'];
    $fecha_inicio = $_POST['fecha_inicio'];
    $fecha_final = $_POST['fecha_final'];
    $cantidad_personas = $_POST['cantidad_personas'];
    $metodo_pago = $_POST['metodo_pago'];
    $estado_reserva = $_POST['estado_reserva'];

    $stmt = $conn->prepare("UPDATE reserva SET nombre_cliente = ?, fecha_inicio = ?, fecha_final = ?, cantidad_personas = ?, metodo_pago = ?, estado_reserva = ? WHERE idreserva = ?");
    $stmt->bind_param("sssissi", $nombre_cliente, $fecha_inicio, $fecha_final, $cantidad_personas, $metodo_pago, $estado_reserva, $id);

    if ($stmt->execute()) {
        echo "<script>alert('Reserva actualizada exitosamente.'); window.location.href='reservar_finca.php';</script>";
    } else {
        echo "<script>alert('Error al actualizar la reserva.'); window.location.href='reservar_finca.php';</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Reserva</title>
    <link rel="stylesheet" href="../css/styles.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootswatch/4.5.2/lux/bootstrap.min.css">
    <style>
        .form-container {
            max-width: 50%; /* Aumentar el tamaño del formulario al 70% */
            margin: 0 auto;
            background-color: #ffffff;
            padding: 2rem;
            border-radius: 15px;
            box-shadow: 0 4px 8px rgba(234, 234, 229, 0.76);
        }
        .form-label {
            font-weight: bold;
            font-size: 18px;
            color: rgb(8, 105, 98); /* Cambiar el color de los labels */
            text-align: center; /* Alinear los labels al centro */
            display: block;
            width: 100%;
            margin-bottom: 0.5rem;
            margin-top: 2px;
        }
        .form-control {
            border-radius: 10px;
            width: 100%; /* Ancho completo de los inputs */
            margin-bottom: 1rem; /* Espacio entre los inputs */
            border: 2px solid #ced4da; /* Dar contorno a los inputs */
            text-align: center; /* Alinear el texto al centro */
            font-size: 18px; /* Aumentar el tamaño del texto a 18px */
        }
        .btn-primary {
            background-color: #28a745;
            border-color: #28a745;
            border-radius: 10px;
            padding: 0.5rem 1.5rem;
        }
        .btn-primary:hover {
            background-color: #218838;
            border-color: #1e7e34;
        }
        .btn-secondary {
            background-color: #6c757d;
            border-color: #6c757d;
            border-radius: 10px;
            padding: 0.5rem 1.5rem;
        }
        .btn-secondary:hover {
            background-color: #5a6268;
            border-color: #545b62;
        }
        .btn-container {
            display: flex;
            justify-content: center; /* Centrar los botones */
            gap: 20px;
        }
        body {
            background: #f0f0f0; /* Fondo más claro */
            color: #333;
            font-family: 'Roboto', sans-serif;
            font-size: 1rem;
        }
        h1 {
            font-size: 2.5rem;
            margin-bottom: 2rem;
            color:rgb(245, 248, 248); /* Cambiar el color del título */
        }
    </style>
</head>
<body class="bg-dark text-white">
    <main class="container my-5">
        <h1 class="text-center mb-4">Editar Reserva</h1>
        <div class="form-container">
            <?php if ($reserva): ?>
                <form action="editar_reserva.php?id=<?php echo $id; ?>" method="POST">
                    <div class="mb-3">
                        <label for="nombre_cliente" class="form-label">Nombre del Cliente</label>
                        <input id="nombre_cliente" name="nombre_cliente" type="text" class="form-control" value="<?php echo $reserva['nombre_cliente']; ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="fecha_inicio" class="form-label">Fecha de inicio</label>
                        <input id="fecha_inicio" name="fecha_inicio" type="datetime-local" class="form-control" value="<?php echo $reserva['fecha_inicio']; ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="fecha_final" class="form-label">Fecha de fin</label>
                        <input id="fecha_final" name="fecha_final" type="datetime-local" class="form-control" value="<?php echo $reserva['fecha_final']; ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="cantidad_personas" class="form-label">Cantidad de personas</label>
                        <input id="cantidad_personas" name="cantidad_personas" type="number" class="form-control" value="<?php echo $reserva['cantidad_personas']; ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="metodo_pago" class="form-label">Método de pago</label>
                        <select id="metodo_pago" name="metodo_pago" class="form-select form-control" required>
                            <option value="credit-card" <?php echo $reserva['metodo_pago'] == 'credit-card' ? 'selected' : ''; ?>>Tarjeta de crédito</option>
                            <option value="paypal" <?php echo $reserva['metodo_pago'] == 'paypal' ? 'selected' : ''; ?>>PayPal</option>
                            <option value="bank-transfer" <?php echo $reserva['metodo_pago'] == 'bank-transfer' ? 'selected' : ''; ?>>Transferencia bancaria</option>
                            <option value="nequi" <?php echo $reserva['metodo_pago'] == 'nequi' ? 'selected' : ''; ?>>Nequi</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="estado_reserva" class="form-label">Estado de la Reserva</label>
                        <select id="estado_reserva" name="estado_reserva" class="form-select form-control" required>
                            <option value="Confirmada" <?php echo $reserva['estado_reserva'] == 'Confirmada' ? 'selected' : ''; ?>>Confirmada</option>
                            <option value="Pendiente" <?php echo $reserva['estado_reserva'] == 'Pendiente' ? 'selected' : ''; ?>>Pendiente</option>
                            <option value="Cancelada" <?php echo $reserva['estado_reserva'] == 'Cancelada' ? 'selected' : ''; ?>>Cancelada</option>
                        </select>
                    </div>
                    <div class="btn-container">
                        <button type="submit" class="btn btn-primary">Actualizar</button>
                        <a href="reservar_finca.php" class="btn btn-secondary">Cancelar</a>
                    </div>
                </form>
            <?php else: ?>
                <p class="text-center text-muted">Reserva no encontrada.</p>
            <?php endif; ?>
        </div>
    </main>
</body>
</html>

<?php
$conn->close();
?>
