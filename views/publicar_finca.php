<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header("Location: ../views/login.php"); // Redirigir al formulario de inicio de sesión si no hay sesión
    exit;
}

// Permitir acceso a proveedores y administradores
$rolesPermitidos = ['proveedor', 'administrador'];
$esPermitido = in_array($_SESSION['roles'][0]['nombre_rol'], $rolesPermitidos);

if (!$esPermitido) {
    header("Location: ../index.php"); // Redirigir al inicio si no tiene permisos
    exit;
}

// Incluir encabezado y conexión a la base de datos
include '../config/database.php';

$conn = new mysqli('localhost', 'root', '', 'lugyser');

// Verificar la conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Depuración: Registrar los datos enviados
    error_log("Datos enviados desde el formulario: " . json_encode($_POST));
    error_log("Archivo subido: " . json_encode($_FILES['imagen_lugar']));

    // Validar los datos del formulario
    $nombre_lugar = trim($_POST['nombre_lugar']);
    $ubicacion_lugar = trim($_POST['ubicacion_lugar']);
    $descripcion_lugar = trim($_POST['descripcion_lugar']);
    $cantidad_habitaciones = intval($_POST['cantidad_habitaciones']);
    $precio_lugar = floatval($_POST['precio_lugar']);
    $tipo = trim($_POST['tipo']);
    $disponibilidad_lugar = isset($_POST['disponibilidad_lugar']) ? 1 : 0;
    $cantidad_banos = intval($_POST['cantidad_banos']);
    $cantidad_piscinas = intval($_POST['cantidad_piscinas']);
    $juegos_infantiles = isset($_POST['juegos_infantiles']) ? 1 : 0;
    $zonas_verdes = isset($_POST['zonas_verdes']) ? 1 : 0;
    $imagen_lugar = $_FILES['imagen_lugar']['name'];
    $usuario_id = $_SESSION['usuario']['idusuario'];

    // Asignar el rol correspondiente
    $rol_id = ($_SESSION['roles'][0]['nombre_rol'] === 'administrador') ? 1 : 2; // 1: Administrador, 2: Proveedor

    // Validar campos obligatorios
    if (empty($nombre_lugar) || empty($ubicacion_lugar) || empty($descripcion_lugar) || empty($imagen_lugar)) {
        echo "<script>alert('Por favor, complete todos los campos obligatorios.'); window.history.back();</script>";
        exit;
    }

    // Mover la imagen subida a la carpeta de destino
    $target_dir = "../uploads/";
    $target_file = $target_dir . basename($imagen_lugar);
    if (!move_uploaded_file($_FILES['imagen_lugar']['tmp_name'], $target_file)) {
        error_log("Error al mover la imagen al directorio destino.");
        echo "<script>alert('Error al subir la imagen.'); window.history.back();</script>";
        exit;
    }

    // Insertar los datos en la tabla `lugar`
    $stmt = $conn->prepare("INSERT INTO lugar (nombre_lugar, ubicacion_lugar, descripcion_lugar, cantidad_habitaciones, precio_lugar, tipo, disponibilidad_lugar, cantidad_banos, cantidad_piscinas, juegos_infantiles, zonas_verdes, imagen_lugar, usuario_has_rol_usuario_idusuario, usuario_has_rol_rol_idrol) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssiisiiiiisii", $nombre_lugar, $ubicacion_lugar, $descripcion_lugar, $cantidad_habitaciones, $precio_lugar, $tipo, $disponibilidad_lugar, $cantidad_banos, $cantidad_piscinas, $juegos_infantiles, $zonas_verdes, $imagen_lugar, $usuario_id, $rol_id);

    if ($stmt->execute()) {
        // Redirigir al dashboard con un indicador de éxito
        header("Location: proveedor_dashboard.php?publicado=true");
        exit;
    } else {
        echo "<script>alert('Error al publicar la finca.'); window.history.back();</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Publicar Finca</title>
    <link rel="stylesheet" href="../css/styles.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootswatch/4.5.2/lux/bootstrap.min.css">
    <style>
        .form-container {
            max-width: 55%; /* Aumentar el ancho del formulario al 55% */
            margin: 0 auto;
            background-color: #ffffff;
            padding: 2rem;
            border-radius: 15px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .form-label {
            font-weight: bold;
            font-size: 18px;
            color:rgb(8, 105, 98); /* Cambiar el color de los labels */
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
        .form-check {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            margin-bottom: 1.5rem;
            justify-content: space-between; /* Asegurar que los elementos se distribuyan uniformemente */
        }
        .form-check-input {
            display: flex;
            flex-direction: column;
            justify-content: center;
            width: 25px; /* Aumentar el tamaño de las casillas checkbox */
            height: 25px; /* Aumentar el tamaño de las casillas checkbox */
            margin-left: 13rem; /* Desplazar los check a un rem de los labels */
            margin-top: 1px; /* Ajustar la posición de los check */
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
            color: #333; /* Cambiar el color del título */
        }
        @media (max-width: 768px) {
            .form-container {
                max-width: 80%;
                padding: 1.5rem;
            }
            .form-label {
                font-size: 16px;
            }
            .form-control {
                font-size: 16px;
            }
        }
        @media (max-width: 576px) {
            .form-container {
                max-width: 95%;
                padding: 1rem;
            }
            .form-label {
                font-size: 14px;
            }
            .form-control {
                font-size: 14px;
            }
            .btn-container {
                flex-direction: column;
                gap: 10px;
            }
        }
    </style>
</head>
<body>

    <div id='header-container'></div> <!-- Aquí se insertará el encabezado -->
    
    <main class="container my-5">
        <h1 class="text-center mb-4">Publicar Finca</h1>
        
        <div class="form-container">
            <form action="../controllers/PublicarFincaController.php" method="POST" enctype="multipart/form-data">
                <div class="mb-3">
                    <label for="nombre_lugar" class="form-label">Nombre del lugar <span class="text-danger"></span></label>
                    <input type="text" id="nombre_lugar" name="nombre_lugar" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label for="ubicacion_lugar" class="form-label">Ubicación del lugar <span class="text-danger"></span></label>
                    <input type="text" id="ubicacion_lugar" name="ubicacion_lugar" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label for="descripcion_lugar" class="form-label">Descripción del lugar <span class="text-danger"></span></label>
                    <textarea id="descripcion_lugar" name="descripcion_lugar" class="form-control" rows="4" required></textarea>
                </div>

                <div class="mb-3">
                    <label for="cantidad_habitaciones" class="form-label">Cantidad de habitaciones <span class="text-danger"></span></label>
                    <input type="number" id="cantidad_habitaciones" name="cantidad_habitaciones" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label for="precio_lugar" class="form-label">Precio del lugar <span class="text-danger"></span></label>
                    <input type="number" id="precio_lugar" name="precio_lugar" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label for="tipo" class="form-label">Tipo <span class="text-danger"></span></label>
                    <input type="text" id="tipo" name="tipo" class="form-control" required>
                </div>

                <div class="form-check">
                    <label for="disponibilidad_lugar" class="form-label">Disponibilidad:</label>
                    <input type="checkbox" id="disponibilidad_lugar" name="disponibilidad_lugar" class="form-check-input">
                </div>

                <div class="mb-3">
                    <label for="cantidad_banos" class="form-label">Cantidad de baños <span class="text-danger"></span></label>
                    <input type="number" id="cantidad_banos" name="cantidad_banos" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label for="cantidad_piscinas" class="form-label">Cantidad de piscinas <span class="text-danger"></span></label>
                    <input type="number" id="cantidad_piscinas" name="cantidad_piscinas" class="form-control" required>
                </div>

                <div class="form-check">
                    <label for="juegos_infantiles" class="form-label">Juegos infantiles</label>
                    <input type="checkbox" id="juegos_infantiles" name="juegos_infantiles" class="form-check-input">
                </div>

                <div class="form-check">
                    <label for="zonas_verdes" class="form-label">Zonas verdes</label>
                    <input type="checkbox" id="zonas_verdes" name="zonas_verdes" class="form-check-input">
                </div>

                <div class="mb-3">
                    <label for="imagen_lugar" class="form-label">Imagen del lugar <span class="text-danger"></span></label>
                    <input type="file" id="imagen_lugar" name="imagen_lugar" class="form-control" required>
                </div>

                <input type="hidden" name="usuario_id" value="1">
                <input type="hidden" name="rol_id" value="1">

                <div class="btn-container">
                    <button class="btn btn-primary" type="submit">Publicar</button>
                    <button class="btn btn-secondary" type="reset">Cancelar</button>
                </div>
            </form>
        </div>
    </main>

    <div id='footer-container'></div> <!-- Aquí se insertará el pie de página -->

    <!-- Bootstrap JavaScript -->
    <script src="https://stackpath.bootstrapcdn.com/bootswatch/4.5.2/lux/bootstrap.min.js"></script>
    <script src="../js/main.js" defer></script>
</body>
</html>

<?php
// Incluir pie de página
include '../includes/footer.php';
?>
