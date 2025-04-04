<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header("Location: ../views/login.php"); // Redirigir al formulario de inicio de sesión si no hay sesión
    exit;
}

// Verificar si el usuario tiene el rol de proveedor o administrador
$rolesPermitidos = ['proveedor', 'administrador'];
$esPermitido = in_array($_SESSION['roles'][0]['nombre_rol'], $rolesPermitidos);

if (!$esPermitido) {
    header("Location: ../index.php"); // Redirigir al inicio si no tiene permisos
    exit;
}

require_once '../config/database.php';

$db = new Database();
$conn = $db->getConnection();

// Verificar si la conexión a la base de datos es válida
if (!$conn) {
    die("Error al conectar con la base de datos.");
}

// Validar que se haya pasado un ID de lugar
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("ID de lugar no válido.");
}

$idLugar = intval($_GET['id']);
$idUsuario = $_SESSION['usuario']['idusuario'];
$esAdministrador = $_SESSION['roles'][0]['nombre_rol'] === 'administrador';

// Verificar que el lugar pertenece al proveedor en sesión o permitir al administrador editar cualquier lugar
$query = $esAdministrador 
    ? "SELECT * FROM lugar WHERE idlugar = :idlugar" 
    : "SELECT * FROM lugar WHERE idlugar = :idlugar AND usuario_has_rol_usuario_idusuario = :idusuario";

$stmt = $conn->prepare($query);
$stmt->bindParam(':idlugar', $idLugar, PDO::PARAM_INT);
if (!$esAdministrador) {
    $stmt->bindParam(':idusuario', $idUsuario, PDO::PARAM_INT);
}

try {
    $stmt->execute();
    $lugar = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$lugar) {
        die("No tienes permiso para editar este lugar o el lugar no existe.");
    }
} catch (PDOException $e) {
    die("Error al verificar el lugar: " . $e->getMessage());
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre_lugar = $_POST['nombre_lugar'];
    $ubicacion_lugar = $_POST['ubicacion_lugar'];
    $descripcion_lugar = $_POST['descripcion_lugar'];
    $cantidad_habitaciones = $_POST['cantidad_habitaciones'];
    $precio_lugar = $_POST['precio_lugar'];
    $tipo = $_POST['tipo'];
    $disponibilidad_lugar = isset($_POST['disponibilidad_lugar']) ? 1 : 0;
    $cantidad_banos = $_POST['cantidad_banos'];
    $cantidad_piscinas = $_POST['cantidad_piscinas'];
    $juegos_infantiles = isset($_POST['juegos_infantiles']) ? 1 : 0;
    $zonas_verdes = isset($_POST['zonas_verdes']) ? 1 : 0;

    $stmt = $conn->prepare("UPDATE lugar SET nombre_lugar = ?, ubicacion_lugar = ?, descripcion_lugar = ?, cantidad_habitaciones = ?, precio_lugar = ?, tipo = ?, disponibilidad_lugar = ?, cantidad_banos = ?, cantidad_piscinas = ?, juegos_infantiles = ?, zonas_verdes = ? WHERE idlugar = ?");
    $stmt->bindValue(1, $nombre_lugar, PDO::PARAM_STR);
    $stmt->bindValue(2, $ubicacion_lugar, PDO::PARAM_STR);
    $stmt->bindValue(3, $descripcion_lugar, PDO::PARAM_STR);
    $stmt->bindValue(4, $cantidad_habitaciones, PDO::PARAM_INT);
    $stmt->bindValue(5, $precio_lugar, PDO::PARAM_STR);
    $stmt->bindValue(6, $tipo, PDO::PARAM_STR);
    $stmt->bindValue(7, $disponibilidad_lugar, PDO::PARAM_INT);
    $stmt->bindValue(8, $cantidad_banos, PDO::PARAM_INT);
    $stmt->bindValue(9, $cantidad_piscinas, PDO::PARAM_INT);
    $stmt->bindValue(10, $juegos_infantiles, PDO::PARAM_INT);
    $stmt->bindValue(11, $zonas_verdes, PDO::PARAM_INT);
    $stmt->bindValue(12, $idLugar, PDO::PARAM_INT);

    if ($stmt->execute()) {
        echo "<script>alert('Lugar actualizado exitosamente.'); window.location.href='listar_fincas.php';</script>";
    } else {
        echo "<script>alert('Error al actualizar el lugar.'); window.location.href='listar_fincas.php';</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Lugar</title>
    <link rel="stylesheet" href="../css/styles.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootswatch/4.5.2/lux/bootstrap.min.css">
    <style>
        .form-container {
            width: 50%; /* Aumentar el ancho del formulario al 55% */
            margin: 0 auto;
            background-color: #ffffff;
            padding: 2rem;
            border-radius: 15px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        @media (max-width: 768px) {
            .form-container {
                width: 90%; /* Ancho del formulario al 90% en pantallas pequeñas */
            }
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
    </style>
</head>
<body>

    <div id='header-container'></div> <!-- Aquí se insertará el encabezado -->
    
    <main class="container my-5">
        <h1 class="text-center mb-4">Editar Lugar</h1>
        
        <div class="form-container">
            <?php if ($lugar): ?>
                <form action="editar_lugar.php?id=<?php echo $lugar_id; ?>" method="POST">
                    <div class="mb-3">
                        <label for="nombre_lugar" class="form-label">Nombre del lugar <span class="text-danger"></span></label>
                        <input type="text" id="nombre_lugar" name="nombre_lugar" class="form-control" value="<?php echo $lugar['nombre_lugar']; ?>" required>
                    </div>

                    <div class="mb-3">
                        <label for="ubicacion_lugar" class="form-label">Ubicación del lugar <span class="text-danger"></span></label>
                        <input type="text" id="ubicacion_lugar" name="ubicacion_lugar" class="form-control" value="<?php echo $lugar['ubicacion_lugar']; ?>" required>
                    </div>

                    <div class="mb-3">
                        <label for="descripcion_lugar" class="form-label">Descripción del lugar <span class="text-danger"></span></label>
                        <textarea id="descripcion_lugar" name="descripcion_lugar" class="form-control" rows="4" required><?php echo $lugar['descripcion_lugar']; ?></textarea>
                    </div>

                    <div class="mb-3">
                        <label for="cantidad_habitaciones" class="form-label">Cantidad de habitaciones <span class="text-danger"></span></label>
                        <input type="number" id="cantidad_habitaciones" name="cantidad_habitaciones" class="form-control" value="<?php echo $lugar['cantidad_habitaciones']; ?>" required>
                    </div>

                    <div class="mb-3">
                        <label for="precio_lugar" class="form-label">Precio del lugar <span class="text-danger"></span></label>
                        <input type="number" id="precio_lugar" name="precio_lugar" class="form-control" value="<?php echo $lugar['precio_lugar']; ?>" required>
                    </div>

                    <div class="mb-3">
                        <label for="tipo" class="form-label">Tipo <span class="text-danger"></span></label>
                        <input type="text" id="tipo" name="tipo" class="form-control" value="<?php echo $lugar['tipo']; ?>" required>
                    </div>

                    <div class="form-check">
                        <input type="checkbox" id="disponibilidad_lugar" name="disponibilidad_lugar" class="form-check-input" <?php echo $lugar['disponibilidad_lugar'] ? 'checked' : ''; ?>>
                        <label for="disponibilidad_lugar" class="form-label">Disponibilidad:</label>
                    </div>

                    <div class="mb-3">
                        <label for="cantidad_banos" class="form-label">Cantidad de baños <span class="text-danger"></span></label>
                        <input type="number" id="cantidad_banos" name="cantidad_banos" class="form-control" value="<?php echo $lugar['cantidad_banos']; ?>" required>
                    </div>

                    <div class="mb-3">
                        <label for="cantidad_piscinas" class="form-label">Cantidad de piscinas <span class="text-danger"></span></label>
                        <input type="number" id="cantidad_piscinas" name="cantidad_piscinas" class="form-control" value="<?php echo $lugar['cantidad_piscinas']; ?>" required>
                    </div>

                    <div class="form-check">
                        <input type="checkbox" id="juegos_infantiles" name="juegos_infantiles" class="form-check-input" <?php echo $lugar['juegos_infantiles'] ? 'checked' : ''; ?>>
                        <label for="juegos_infantiles" class="form-label">Juegos infantiles</label>
                    </div>

                    <div class="form-check">
                        <input type="checkbox" id="zonas_verdes" name="zonas_verdes" class="form-check-input" <?php echo $lugar['zonas_verdes'] ? 'checked' : ''; ?>>
                        <label for="zonas_verdes" class="form-label">Zonas verdes</label>
                    </div>

                    <div class="btn-container">
                        <button class="btn btn-primary" type="submit">Actualizar</button>
                        <a href="listar_fincas.php" class="btn btn-secondary">Cancelar</a>
                    </div>
                </form>
            <?php else: ?>
                <p class="text-center text-muted">Lugar no encontrado.</p>
            <?php endif; ?>
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
